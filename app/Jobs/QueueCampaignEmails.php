<?php

namespace App\Jobs;

use App\Models\CampaignMessage;
use App\Models\CampaignRecipientSend;
use App\Models\EmailRecipient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class QueueCampaignEmails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 120;

    public function __construct(
        public readonly int $campaignMessageId,
        public readonly ?int $limit = null,
    ) {
    }

    public function handle(): void
    {
        $campaign = CampaignMessage::find($this->campaignMessageId);
        if (!$campaign) {
            return;
        }

        $spacingSeconds = (int) config('campaign.send_spacing_seconds', 2);
        $start = now();
        $offset = 0;
        $campaignId = $campaign->id;
        $remaining = $this->limit;

        EmailRecipient::whereNotExists(function ($query) use ($campaign) {
                $query->selectRaw(1)
                    ->from('campaign_recipient_sends')
                    ->whereColumn('campaign_recipient_sends.email_recipient_id', 'email_recipients.id')
                    ->where('campaign_recipient_sends.campaign_message_id', $campaign->id)
                    ->whereNotNull('campaign_recipient_sends.sent_at');
            })
            ->orderBy('id')
            ->chunkById(200, function ($recipients) use ($spacingSeconds, $start, &$offset, $campaignId, &$remaining) {
                foreach ($recipients as $recipient) {
                    if ($remaining !== null && $remaining <= 0) {
                        return false;
                    }

                    $delay = $start->copy()->addSeconds($offset * $spacingSeconds);

                    SendCampaignEmail::dispatch($recipient->id, $campaignId)
                        ->delay($delay);

                    $offset++;

                    if ($remaining !== null) {
                        $remaining--;
                    }
                }

                return $remaining === null || $remaining > 0;
            });
    }
}
