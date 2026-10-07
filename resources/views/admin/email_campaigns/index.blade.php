@extends('admin.layouts.app')

@section('title', 'Email Campaigns')

@push('styles')
    <style>
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: #2c3e50;
        }

        .page-subtitle {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .preview-box {
            background: #f8f9fb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 20px;
        }

        .preview-body p {
            margin-bottom: 12px;
        }

        .preview-body ul {
            margin-bottom: 12px;
        }

        .queue-status {
            font-size: 13px;
        }

        .preview-modal .modal-dialog {
            max-width: 100%;
            width: 100%;
            height: 100%;
            margin: 0;
        }

        .preview-modal .modal-content {
            height: 100%;
            border-radius: 0;
        }

        .preview-modal .modal-body {
            padding: 24px;
            overflow-y: auto;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1 class="page-title">Email Campaigns</h1>
        <p class="page-subtitle">Manage campaign content and send bulk emails.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted text-uppercase small">Total Recipients</div>
                    <div id="stat-total" class="fs-3 fw-semibold">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted text-uppercase small">Queued / Pending</div>
                    <div id="stat-pending" class="fs-3 fw-semibold">{{ $stats['pending'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted text-uppercase small">Sent</div>
                    <div id="stat-sent" class="fs-3 fw-semibold">{{ $stats['sent'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">Campaign Message</span>
                    @if ($isNewCampaign)
                        <a href="{{ route('admin.email-campaigns.index') }}" class="btn btn-sm btn-outline-secondary">
                            Back to Active Campaign
                        </a>
                    @else
                        <a href="{{ route('admin.email-campaigns.index', ['new' => 1]) }}" class="btn btn-sm btn-outline-primary">
                            Add New Campaign
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.email-campaigns.message') }}" method="POST">
                        @csrf
                        @if ($isNewCampaign)
                            <input type="hidden" name="force_new" value="1" />
                        @elseif ($content->campaignId)
                            <input type="hidden" name="campaign_id" value="{{ $content->campaignId }}" />
                        @endif
                        <div class="mb-3">
                            <label class="form-label">Campaign Slug</label>
                            <input
                                type="text"
                                name="slug"
                                class="form-control"
                                value="{{ old('slug', $content->slug) }}"
                                placeholder="kerala-development-video-contest"
                                {{ (!$isNewCampaign && $content->campaignId) ? 'readonly' : '' }}
                            />
                            <div class="form-text">
                                @if (!$isNewCampaign && $content->campaignId)
                                    Slug is locked after saving. Use "Add New Campaign" to create a new one.
                                @else
                                    Leave blank to auto-generate from the subject.
                                @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input
                                type="text"
                                name="subject"
                                class="form-control"
                                value="{{ old('subject', $content->subject) }}"
                                required
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Body</label>
                            <textarea
                                name="body"
                                rows="10"
                                class="form-control"
                                required
                            >{{ old('body', $content->body) }}</textarea>
                        </div>
                        <div class="border rounded p-3 mb-3">
                            <div class="form-check form-switch mb-3">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    id="setActive"
                                    name="set_active"
                                    value="1"
                                    {{ old('set_active', $activeCampaign?->id === $content->campaignId) ? 'checked' : '' }}
                                />
                                <label class="form-check-label" for="setActive">Set as active campaign</label>
                            </div>
                            <div class="form-check form-switch mb-3">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    role="switch"
                                    id="buttonEnabled"
                                    name="button_enabled"
                                    value="1"
                                    {{ old('button_enabled', $content->buttonEnabled) ? 'checked' : '' }}
                                />
                                <label class="form-check-label" for="buttonEnabled">Enable action button</label>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Button Label</label>
                                <input
                                    type="text"
                                    name="button_label"
                                    class="form-control"
                                    value="{{ old('button_label', $content->buttonLabel) }}"
                                    placeholder="Participate Now"
                                />
                            </div>
                            <div class="mb-0">
                                <label class="form-label">Button Link</label>
                                <input
                                    type="text"
                                    name="button_url"
                                    class="form-control"
                                    value="{{ old('button_url', $content->buttonUrl) }}"
                                    placeholder="https://example.com"
                                />
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Message</button>
                        <button type="button" class="btn btn-outline-secondary ml-2" data-toggle="modal" data-target="#emailPreviewModal">
                            Preview Email
                        </button>
                    </form>
                    <div class="text-muted small mt-3">
                        Use <strong>[Name]</strong> to personalize greetings (e.g. Dear [Name],).
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">Campaigns</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Slug</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($campaigns as $campaign)
                                    <tr>
                                        <td>{{ $campaign->slug }}</td>
                                        <td>{{ $campaign->subject }}</td>
                                        <td>
                                            @if ($campaign->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $campaign->created_at?->format('d M Y') }}</td>
                                        <td class="text-end">
                                            @if (!$campaign->is_active)
                                                <form action="{{ route('admin.email-campaigns.activate', $campaign->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Make Active</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-muted">No campaigns created yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-semibold">Send Email</div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="fw-semibold">
                                Delivery Progress
                                @if ($activeCampaign)
                                    <span class="badge bg-primary text-white ms-2">{{ $activeCampaign->slug }}</span>
                                @endif
                            </div>
                            <div class="text-muted small">
                                <span id="delivery-progress-sent">{{ $stats['sent'] }}</span> /
                                <span id="delivery-progress-total">{{ $stats['total'] }}</span>
                            </div>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div
                                id="delivery-progress-bar"
                                class="progress-bar"
                                role="progressbar"
                                style="width: {{ $progressPercent }}%;"
                                aria-valuenow="{{ $progressPercent }}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                        </div>
                        <div class="text-muted small mt-2">
                            <span id="delivery-progress-percent">{{ $progressPercent }}</span>% completed.
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <form id="queue-form" action="{{ route('admin.email-campaigns.send') }}" method="POST" class="d-flex flex-wrap align-items-end gap-2">
                            @csrf
                            <div class="form-group mb-0" style="max-width: 220px;">
                                <label class="form-label small text-muted">Send limit (optional)</label>
                                <input
                                    type="number"
                                    name="send_limit"
                                    min="1"
                                    class="form-control form-control-sm"
                                    placeholder="e.g. 200"
                                    value="{{ old('send_limit') }}"
                                />
                            </div>
                            <button id="queue-button" type="submit" class="btn btn-outline-secondary">Queue Emails</button>
                        </form>
                    </div>
                    <div id="queue-progress" class="d-none">
                        <div class="d-flex align-items-center gap-2">
                            <div class="spinner-border spinner-border-sm text-secondary" role="status" aria-hidden="true"></div>
                            <div id="queue-status" class="queue-status text-muted">Queueing emails...</div>
                        </div>
                    </div>
                    <div class="text-muted small mt-3">
                        Leave the limit blank to send to everyone. Queue spacing is {{ config('campaign.send_spacing_seconds') }} seconds per email.
                    </div>
                    <hr class="my-4">
                    <form action="{{ route('admin.email-campaigns.test') }}" method="POST" class="d-grid gap-2">
                        @csrf
                        <input
                            type="email"
                            name="test_email"
                            class="form-control"
                            placeholder="name@example.com"
                            value="{{ old('test_email') }}"
                            required
                        />
                        <button type="submit" class="btn btn-outline-secondary mt-2">Send Test Email</button>
                    </form>
                    <div class="text-muted small mt-2">Sends to the entered address only.</div>
                </div>
            </div>

        </div>
    </div>

    <div class="modal fade preview-modal" id="emailPreviewModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Email Preview</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="preview-box">
                        <img src="{{ $logoUrl }}" alt="Ente Keralam Logo" style="height: 60px; width: auto;">
                        <div class="preview-body mt-3">{!! $previewHtml !!}</div>
                        @if ($content->buttonEnabled)
                            <div class="mt-3">
                                <a
                                    href="{{ $content->buttonUrl }}"
                                    target="_blank"
                                    style="background-color:#ff0099; color:#ffffff; text-decoration:none; padding:10px 18px; border-radius:6px; display:inline-block; font-weight:600;"
                                >
                                    {{ $content->buttonLabel }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const queueForm = document.getElementById('queue-form');
            const queueProgress = document.getElementById('queue-progress');
            const queueStatus = document.getElementById('queue-status');
            const queueButton = document.getElementById('queue-button');
            let queueTarget = null;
            let queueActive = false;

            if (queueForm && window.fetch) {
                queueForm.addEventListener('submit', async (event) => {
                    event.preventDefault();
                    queueProgress.classList.remove('d-none');
                    queueStatus.textContent = 'Queueing emails...';
                    queueButton.disabled = true;
                    queueActive = true;
                    queueTarget = null;

                    try {
                        const response = await fetch(queueForm.action, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: new FormData(queueForm),
                        });

                        if (!response.ok) {
                            throw new Error('Queue request failed');
                        }

                        const data = await response.json();
                        queueStatus.textContent = data.message || 'Queue started.';
                        const queued = Number(data.queued ?? 0);
                        const sentSoFar = Number(data.sent ?? document.getElementById('stat-sent')?.textContent ?? 0);
                        if (Number.isFinite(queued) && queued > 0) {
                            queueTarget = sentSoFar + queued;
                        } else {
                            queueActive = false;
                            queueTarget = null;
                            queueButton.disabled = false;
                        }
                    } catch (error) {
                        queueStatus.textContent = 'Queueing failed. Please try again.';
                        queueActive = false;
                        queueTarget = null;
                        queueButton.disabled = false;
                    } finally {
                        if (!queueActive) {
                            queueButton.disabled = false;
                        }
                    }
                });
            }

            const updateStats = (data) => {
                const total = document.getElementById('stat-total');
                const pending = document.getElementById('stat-pending');
                const sent = document.getElementById('stat-sent');
                const bar = document.getElementById('delivery-progress-bar');
                const sentText = document.getElementById('delivery-progress-sent');
                const totalText = document.getElementById('delivery-progress-total');
                const percentText = document.getElementById('delivery-progress-percent');

                if (total) total.textContent = data.total;
                if (pending) pending.textContent = data.pending;
                if (sent) sent.textContent = data.sent;
                if (bar) {
                    bar.style.width = `${data.percent}%`;
                    bar.setAttribute('aria-valuenow', data.percent);
                }
                if (sentText) sentText.textContent = data.sent;
                if (totalText) totalText.textContent = data.total;
                if (percentText) percentText.textContent = data.percent;

                if (queueActive && queueTarget !== null && queueButton) {
                    const processed = Number(data.sent ?? 0) + Number(data.failed ?? 0);
                    if (processed >= queueTarget) {
                        queueActive = false;
                        queueTarget = null;
                        queueButton.disabled = false;
                        if (queueStatus) {
                            queueStatus.textContent = 'Queue completed.';
                        }
                    }
                }
            };

            const poll = async () => {
                try {
                    const response = await fetch('{{ route('admin.email-campaigns.progress') }}', {
                        headers: { 'Accept': 'application/json' },
                    });
                    if (!response.ok) {
                        return;
                    }
                    const data = await response.json();
                    updateStats(data);
                } catch (error) {
                    // ignore polling errors
                }
            };

            poll();
            setInterval(poll, 5000);
        });
    </script>
@endpush
