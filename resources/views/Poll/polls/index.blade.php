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
<div class="page-header">
    <h1 class="page-title">Manage Polls</h1>
    <p class="page-subtitle">Create and manage polls for the admin panel</p>
</div>

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
            <h5 class="mb-0">Poll List</h5>
            <a href="{{ route('admin.polls.create') }}" class="btn btn-sm bg-primary text-white">
                <i class="fas fa-plus-circle me-1"></i> Add New Poll
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="pollTable" class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Festival</th>
                            <th>Event</th>
                            <th>Poll Name</th>
                            <th>Type</th>
                            <th>Topic</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th class="text-center" width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($polls as $poll)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $poll->festival->name ?? '-' }}</td>
                                <td>{{ $poll->event->name ?? '-' }}</td>
                                <td>{{ $poll->name }}</td>
                                <td><span class="badge bg-info">{{ $poll->section_type ?? '—' }}</span></td>
                                <td>{{ $poll->topic ?? '—' }}</td>
                                <td>
                                    <small>
                                        <strong>{{ $poll->start_date }}</strong> {{ $poll->start_time ? date('h:i A', strtotime($poll->start_time)) : '' }}
                                        <br>
                                        <span class="text-muted">to</span>
                                        <br>
                                        <strong>{{ $poll->end_date }}</strong> {{ $poll->end_time ? date('h:i A', strtotime($poll->end_time)) : '' }}
                                    </small>
                                </td>
                                <td>
                                    @if($poll->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.polls.show', $poll->id) }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.polls.edit', $poll->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.polls.destroy', $poll->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this poll?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No polls found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $polls->links('pagination::bootstrap-5') }}
            </div>
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
