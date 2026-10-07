<?php

namespace App\Services;

use App\Models\CampaignMessage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class CampaignContent
{
    public function __construct(
        public readonly string $subject,
        public readonly string $body,
        public readonly ?int $campaignId = null,
        public readonly string $slug = 'default-campaign',
        public readonly bool $buttonEnabled = true,
        public readonly string $buttonLabel = 'Participate Now',
        public readonly string $buttonUrl = 'https://entekeralam.kerala.gov.in/competition-details/kerala-development-video-contest',
    ) {
    }

    public static function load(?string $path = null): self
    {
        $path = $path ?? config('campaign.mail_content_path', storage_path('app/campaign/mail-content.txt'));
        $buttonDefaults = config('campaign.button', []);

        if (Schema::hasTable('campaign_messages')) {
            $message = CampaignMessage::where('is_active', true)->latest('id')->first();
            if ($message) {
                return new self(
                    $message->subject,
                    $message->body,
                    $message->id,
                    (string) ($message->slug ?? 'default-campaign'),
                    (bool) ($message->button_enabled ?? ($buttonDefaults['enabled'] ?? true)),
                    (string) ($message->button_label ?? ($buttonDefaults['label'] ?? 'Participate Now')),
                    (string) ($message->button_url ?? ($buttonDefaults['url'] ?? 'https://entekeralam.kerala.gov.in/competition-details/kerala-development-video-contest'))
                );
            }
        }

        if (File::exists($path)) {
            $contents = File::get($path);
            [$subject, $body] = self::parse($contents);
            $buttonEnabled = (bool) ($buttonDefaults['enabled'] ?? true);
            $buttonLabel = (string) ($buttonDefaults['label'] ?? 'Participate Now');
            $buttonUrl = (string) ($buttonDefaults['url'] ?? 'https://entekeralam.kerala.gov.in/competition-details/kerala-development-video-contest');
            $slug = Str::slug($subject) ?: 'default-campaign';

            if (Schema::hasTable('campaign_messages')) {
                CampaignMessage::create([
                    'slug' => $slug,
                    'subject' => $subject,
                    'body' => $body,
                    'button_enabled' => $buttonEnabled,
                    'button_label' => $buttonLabel,
                    'button_url' => $buttonUrl,
                    'is_active' => true,
                ]);
            }

            return new self($subject, $body, null, $slug, $buttonEnabled, $buttonLabel, $buttonUrl);
        }

        $buttonEnabled = (bool) ($buttonDefaults['enabled'] ?? true);
        $buttonLabel = (string) ($buttonDefaults['label'] ?? 'Participate Now');
        $buttonUrl = (string) ($buttonDefaults['url'] ?? 'https://entekeralam.kerala.gov.in/competition-details/kerala-development-video-contest');

        return new self('Campaign', '', null, 'default-campaign', $buttonEnabled, $buttonLabel, $buttonUrl);
    }

    public static function parse(string $contents): array
    {
        $lines = preg_split('/\r\n|\n|\r/', trim($contents));

        $subject = 'Campaign';
        if (!empty($lines)) {
            $firstLine = array_shift($lines);
            if (Str::startsWith($firstLine, ['Subject:', 'subject:'])) {
                $subject = trim(Str::after($firstLine, ':'));
            } else {
                array_unshift($lines, $firstLine);
            }
        }

        while (!empty($lines) && trim($lines[0]) === '') {
            array_shift($lines);
        }

        $body = trim(implode("\n", $lines));

        return [$subject, $body];
    }

    public static function personalize(string $markdown, ?string $name): string
    {
        $trimmedName = trim((string) $name);

        if ($trimmedName === '') {
            $markdown = preg_replace('/Dear\s+All,?/i', 'Dear [Name],', $markdown) ?? $markdown;

            return preg_replace('/\[(name)\]/i', '[Name]', $markdown) ?? $markdown;
        }

        $markdown = preg_replace('/Dear\s+All,?/i', "Dear {$trimmedName},", $markdown) ?? $markdown;

        return preg_replace('/\[(name)\]/i', $trimmedName, $markdown) ?? $markdown;
    }

    public static function renderHtml(string $markdown): string
    {
        $html = Str::markdown($markdown);

        return self::applyInlineStyles($html);
    }

    private static function applyInlineStyles(string $html): string
    {
        $replacements = [
            '<h1>' => '<h1 style="margin:0 0 12px; font-size:20px; font-weight:700;">',
            '<h2>' => '<h2 style="margin:0 0 10px; font-size:18px; font-weight:700;">',
            '<h3>' => '<h3 style="margin:0 0 8px; font-size:16px; font-weight:700;">',
            '<p>' => '<p style="margin:0 0 12px;">',
            '<ul>' => '<ul style="margin:0 0 12px 18px; padding:0;">',
            '<ol>' => '<ol style="margin:0 0 12px 18px; padding:0;">',
            '<li>' => '<li style="margin:0 0 6px;">',
            '<strong>' => '<strong style="font-weight:700;">',
        ];

        $html = str_replace(array_keys($replacements), array_values($replacements), $html);

        return preg_replace(
            '/<em>(.*?)<\/em>/s',
            '<strong style="font-weight:700;">$1</strong>',
            $html
        ) ?? $html;
    }
}
