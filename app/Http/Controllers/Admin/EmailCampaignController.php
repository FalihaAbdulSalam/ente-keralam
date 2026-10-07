<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\QueueCampaignEmails;
use App\Models\AdminMenu;
use App\Models\CampaignMessage;
use App\Models\CampaignRecipientSend;
use App\Models\EmailRecipient;
use App\Services\CampaignContent;
use App\Services\CampaignRecipientImporter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Email as EmailRule;

class EmailCampaignController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        $menus = AdminMenu::getMenuTreeForRole($admin->role);
        $logoSetting = config('campaign.logo_public_path', 'images/ente-keralam.png');
        $logoUrl = Str::startsWith($logoSetting, ['http://', 'https://'])
            ? $logoSetting
            : url($logoSetting);
        $activeCampaign = CampaignMessage::where('is_active', true)->latest('id')->first();
        $isNewCampaign = $request->boolean('new');
        $content = $isNewCampaign
            ? $this->buildNewCampaignContent($activeCampaign)
            : CampaignContent::load();
        $stats = [
            'total' => EmailRecipient::count(),
            'sent' => $activeCampaign
                ? CampaignRecipientSend::where('campaign_message_id', $activeCampaign->id)
                    ->whereNotNull('sent_at')
                    ->count()
                : 0,
        ];
        $stats['pending'] = max($stats['total'] - $stats['sent'], 0);
        $progressPercent = $stats['total'] > 0
            ? round(($stats['sent'] / $stats['total']) * 100, 1)
            : 0;

        $previewBody = CampaignContent::personalize($content->body, null);
        $previewHtml = new HtmlString(CampaignContent::renderHtml($previewBody));
        $campaigns = CampaignMessage::orderByDesc('created_at')->get();

        return view('admin.email_campaigns.index', [
            'menus' => $menus,
            'content' => $content,
            'campaigns' => $campaigns,
            'activeCampaign' => $activeCampaign,
            'isNewCampaign' => $isNewCampaign,
            'logoUrl' => $logoUrl,
            'stats' => $stats,
            'progressPercent' => $progressPercent,
            'previewHtml' => $previewHtml,
        ]);
    }

    public function updateMessage(Request $request)
    {
        if (!Schema::hasTable('campaign_messages')) {
            return back()->withErrors(['subject' => 'Campaign message table not found. Run migrations first.']);
        }

        $data = $request->validate([
            'slug' => ['nullable', 'string', 'max:120'],
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'button_enabled' => ['nullable', 'boolean'],
            'button_label' => ['nullable', 'string', 'max:80'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'set_active' => ['nullable', 'boolean'],
            'force_new' => ['nullable', 'boolean'],
            'campaign_id' => ['nullable', 'integer', 'exists:campaign_messages,id'],
        ]);

        $forceNew = (bool) ($data['force_new'] ?? false);
        $existing = null;

        if (!$forceNew && !empty($data['campaign_id'])) {
            $existing = CampaignMessage::find($data['campaign_id']);
        }

        $inputSlug = Str::slug($data['slug'] ?: $data['subject']);
        if ($inputSlug === '') {
            $inputSlug = 'campaign-'.now()->format('YmdHis');
        }

        if (!$existing && !$forceNew) {
            $existing = CampaignMessage::where('slug', $inputSlug)->first();
        }

        $slug = $existing?->slug ?: $inputSlug;

        if (!$existing) {
            $baseSlug = $slug;
            $suffix = 1;
            while (CampaignMessage::where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$suffix;
                $suffix++;
            }
        }

        $buttonDefaults = config('campaign.button', []);
        $buttonEnabled = (bool) ($data['button_enabled'] ?? false);
        $buttonLabel = $data['button_label'] ?? ($buttonDefaults['label'] ?? 'Participate Now');
        $buttonUrl = $data['button_url'] ?? ($buttonDefaults['url'] ?? 'https://entekeralam.kerala.gov.in/competition-details/kerala-development-video-contest');

        if ($buttonEnabled) {
            $request->validate([
                'button_label' => ['required', 'string', 'max:80'],
                'button_url' => ['required', 'string', 'max:255'],
            ]);
        }

        $setActive = (bool) ($data['set_active'] ?? false);
        if (!$setActive && !CampaignMessage::where('is_active', true)->exists()) {
            $setActive = true;
        }

        $campaign = $existing ?? new CampaignMessage();
        $campaign->fill([
            'slug' => $slug,
            'subject' => $data['subject'],
            'body' => $data['body'],
            'button_enabled' => $buttonEnabled,
            'button_label' => $buttonLabel,
            'button_url' => $buttonUrl,
            'is_active' => $setActive,
        ])->save();

        if ($setActive) {
            CampaignMessage::where('id', '!=', $campaign->id)->update(['is_active' => false]);
        }

        return back()->with('status', 'Campaign message updated.');
    }

    public function import(CampaignRecipientImporter $importer)
    {
        $sources = config('campaign.sources', []);

        if (empty($sources)) {
            $result = $importer->import();

            return back()->with(
                'status',
                "Imported recipients. Processed {$result['processed']}, upserted {$result['upserted']}, skipped {$result['skipped']}."
            );
        }

        $summary = [];
        foreach ($sources as $source => $path) {
            $result = $importer->import($path, 500, (string) $source);
            $summary[] = "{$source}: {$result['upserted']} upserted, {$result['skipped']} skipped";
        }

        return back()->with('status', 'Imported recipients. '.implode(' | ', $summary).'.');
    }

    public function send(Request $request)
    {
        $campaign = CampaignMessage::where('is_active', true)->latest('id')->first();
        if (!$campaign) {
            return back()->withErrors(['subject' => 'No active campaign found.']);
        }

        $data = $request->validate([
            'send_limit' => ['nullable', 'integer', 'min:1', 'max:100000'],
        ]);

        $limit = $data['send_limit'] ?? null;
        QueueCampaignEmails::dispatch($campaign->id, $limit);

        $total = EmailRecipient::count();
        $sentCount = CampaignRecipientSend::where('campaign_message_id', $campaign->id)
            ->whereNotNull('sent_at')
            ->count();
        $pending = max($total - $sentCount, 0);
        $queuedCount = $limit ? min($limit, $pending) : $pending;
        $message = "Queueing started for {$queuedCount} emails.";

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'pending' => $pending,
                'queued' => $queuedCount,
                'sent' => $sentCount,
                'total' => $total,
            ]);
        }

        return back()->with('status', $message);
    }

    public function test(Request $request)
    {
        $styles = config('campaign.email_validation', []);
        $rule = $this->buildEmailRule($styles);

        $data = $request->validate([
            'test_email' => ['required', $rule],
        ]);

        $content = CampaignContent::load();
        $personalizedBody = CampaignContent::personalize($content->body, 'Test Recipient');
        $bodyHtml = CampaignContent::renderHtml($personalizedBody);
        $logoSetting = config('campaign.logo_public_path', 'images/ente-keralam.png');
        $logoUrl = Str::startsWith($logoSetting, ['http://', 'https://'])
            ? $logoSetting
            : url($logoSetting);

        Mail::to($data['test_email'])->send(
            new \App\Mail\CampaignMail(
                $content->subject,
                $bodyHtml,
                $logoUrl,
                $content->buttonEnabled,
                $content->buttonLabel,
                $content->buttonUrl
            )
        );

        return back()->with('status', "Test email sent to {$data['test_email']}.");
    }

    public function progress()
    {
        $campaign = CampaignMessage::where('is_active', true)->latest('id')->first();
        $total = EmailRecipient::count();
        $sent = $campaign
            ? CampaignRecipientSend::where('campaign_message_id', $campaign->id)
                ->whereNotNull('sent_at')
                ->count()
            : 0;
        $failed = $campaign
            ? CampaignRecipientSend::where('campaign_message_id', $campaign->id)
                ->whereNotNull('failed_at')
                ->count()
            : 0;
        $pending = max($total - $sent, 0);
        $percent = $total > 0 ? round(($sent / $total) * 100, 1) : 0;

        return response()->json([
            'total' => $total,
            'sent' => $sent,
            'failed' => $failed,
            'pending' => $pending,
            'percent' => $percent,
            'campaign' => $campaign?->slug,
        ]);
    }

    public function activate(CampaignMessage $campaign)
    {
        CampaignMessage::query()->update(['is_active' => false]);
        $campaign->update(['is_active' => true]);

        return back()->with('status', 'Active campaign updated.');
    }

    private function buildEmailRule(array $styles): EmailRule
    {
        $rule = EmailRule::default();
        $styles = array_map(
            fn (string $style) => Str::lower(trim($style)),
            $styles
        );

        if (in_array('strict', $styles, true)) {
            $rule->strict();
        } elseif (in_array('rfc', $styles, true)) {
            $rule->rfcCompliant();
        }

        if (in_array('dns', $styles, true)) {
            $rule->validateMxRecord();
        }

        if (in_array('spoof', $styles, true)) {
            $rule->preventSpoofing();
        }

        if (in_array('filter_unicode', $styles, true)) {
            $rule->withNativeValidation(true);
        } elseif (in_array('filter', $styles, true)) {
            $rule->withNativeValidation();
        }

        return $rule;
    }

    private function buildNewCampaignContent(?CampaignMessage $activeCampaign): CampaignContent
    {
        $buttonDefaults = config('campaign.button', []);
        $buttonEnabled = (bool) ($buttonDefaults['enabled'] ?? true);
        $buttonLabel = (string) ($buttonDefaults['label'] ?? 'Participate Now');
        $buttonUrl = (string) ($buttonDefaults['url'] ?? 'https://entekeralam.kerala.gov.in/competition-details/kerala-development-video-contest');

        if ($activeCampaign) {
            return new CampaignContent(
                $activeCampaign->subject,
                $activeCampaign->body,
                null,
                '',
                (bool) ($activeCampaign->button_enabled ?? $buttonEnabled),
                (string) ($activeCampaign->button_label ?? $buttonLabel),
                (string) ($activeCampaign->button_url ?? $buttonUrl)
            );
        }

        return new CampaignContent('', '', null, '', $buttonEnabled, $buttonLabel, $buttonUrl);
    }
}
