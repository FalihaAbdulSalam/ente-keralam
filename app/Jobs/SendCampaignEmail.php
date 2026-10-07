<?php

namespace App\Jobs;

use App\Mail\CampaignMail;
use App\Models\CampaignMessage;
use App\Models\CampaignRecipientSend;
use App\Models\EmailRecipient;
use App\Services\CampaignContent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        public readonly int $recipientId,
        public readonly int $campaignMessageId,
    ) {
    }

    public function handle(): void
    {
        $recipient = EmailRecipient::find($this->recipientId);
        if (!$recipient || !$recipient->email) {
            return;
        }

        $campaign = CampaignMessage::find($this->campaignMessageId);
        if (!$campaign) {
            return;
        }

        $sendRecord = CampaignRecipientSend::firstOrCreate(
            [
                'campaign_message_id' => $campaign->id,
                'email_recipient_id' => $recipient->id,
            ],
            [
                'last_attempted_at' => now(),
            ]
        );

        if ($sendRecord->sent_at) {
            return;
        }

        $sendRecord->forceFill([
            'last_attempted_at' => now(),
        ])->save();

        $personalizedBody = CampaignContent::personalize($campaign->body, $recipient->name);
        $bodyHtml = CampaignContent::renderHtml($personalizedBody);
        $logoSetting = config('campaign.logo_public_path', 'images/ente-keralam.png');
        $logoUrl = Str::startsWith($logoSetting, ['http://', 'https://'])
            ? $logoSetting
            : url($logoSetting);

        try {
            Mail::to($recipient->email)->send(
                new CampaignMail(
                    $campaign->subject,
                    $bodyHtml,
                    $logoUrl,
                    (bool) $campaign->button_enabled,
                    (string) $campaign->button_label,
                    (string) $campaign->button_url
                )
            );

            $sendRecord->forceFill([
                'sent_at' => now(),
                'failed_at' => null,
                'error_message' => null,
            ])->save();
        } catch (\Throwable $exception) {
            $sendRecord->forceFill([
                'failed_at' => now(),
                'error_message' => substr((string) $exception->getMessage(), 0, 65000),
            ])->save();

            throw $exception;
        }
    }
}
