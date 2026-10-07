@extends('admin.layouts.app')

@section('title', 'Manage Polls')

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
</style>
@endpush

@section('content')

<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">
    
    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ✅ Main Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Contest Submission Details</h5>
        </div>


        <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <dl class="row mb-0">
                                <dt class="col-sm-4">Submission ID:</dt>
                                <dd class="col-sm-8">#{{ $contest->id }}</dd>
                                <dt class="col-sm-4">Title:</dt>
                                <dd class="col-sm-8">{{ $contest->title }}</dd>
                                <dt class="col-sm-4">Description:</dt>
                                <dd class="col-sm-8">{{ $contest->description }}</dd>
                                <dt class="col-sm-4">Document:</dt>
                                <dd class="col-sm-8">
                                    @if($contest->pdf_file)
                                        <a href="{{ asset('uploads/poem/'.$contest->pdf_file) }}" target="_blank">View PDF</a>
                                    @else
                                        No file
                                    @endif
                                </dd>
                                <dt class="col-sm-4">Submitted At:</dt>
                                <dd class="col-sm-8">{{ $contest->created_at->format('F d, Y h:i A') }}</dd>
<dt class="col-sm-4">Status:</dt>
                                <dd class="col-sm-8">
                                    @php
                                        $statusColors = [
                                            '0' => 'warning',
                                            '1' => 'success',
                                            '-1' => 'danger',
                                            '2' => 'info'
                                        ];
                                        $color = $statusColors[$contest->status] ?? 'secondary';
                                        $status = [
                                            '0' => 'Pending',
                                            '1' => 'Approved',
                                            '-1' => 'Rejected'
                                        ];
                                        $status = $status[$contest->status] ?? 'secondary';
                                    @endphp
                                    <button class="btn btn-sm btn-{{ $color }}">{{ ucfirst(str_replace('_', ' ', $status)) }}</button>
                                </dd>
                                </dl>
                        </div>
                        <div class="col-md-6">
                            @if($contest->submitted_by)
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Submitted By:</dt>
                                    <dd class="col-sm-8">
                                        @if($contest->user)
                                            <a href="{{ route('admin.users.show', $contest->user->id) }}">
                                                {{ $contest->user->name }}
                                            </a>
                                            <small class="d-block text-muted">{{ $contest->user->email }}</small>
                                        @else
                                            User ID: {{ $contest->submitted_by }}
                                        @endif
                                    </dd>
                                </dl>
                            @endif

                            @if($contest->contest)
                                <dl class="row mb-0">
                                    <dt class="col-sm-4">Contest:</dt>
                                    <dd class="col-sm-8">
                                        <a href="{{ route('admin.contests.show', $contest->contest->id) }}">
                                            {{ $contest->contest->title }}
                                        </a>
                                    </dd>
                                </dl>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        <div class="card-body">
            
        </div>
    </div>
</div>
@endsection

{{-- ✅ Scripts --}}
@section('scripts')
<script>
$(document).ready(function() {
    $('#pollTable').DataTable({
        "pageLength": 10,
        "ordering": true,
        "searching": true,
        "info": true,
        "responsive": true,
        "autoWidth": false,
        "language": {
            "search": "🔍 Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ polls",
            "zeroRecords": "No matching polls found"
        }
    });
});
</script>
@endsection
