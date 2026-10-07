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
            <h5 class="mb-0">Contest List</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="pollTable" class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Participent</th>
                            <th>Posted Date</th>
                            <th>Status</th>
                            <th class="text-center" width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contest as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->title }}</td>
                                <td>{{ $row->name }} ({{ $row->email }})</td>
                                <td>{{ $row->created_at }}</td>
                                <td>
                                    @if($row->status == 1)
                                        <button class="btn btn-sm btn-success">Approved</button>
                                    @elseif($row->status == 0)
                                        <button class="btn btn-sm btn-warning">Pending</button>
                                    @else
                                        <button class="btn btn-sm btn-danger">Rejected</button>
                                    @endif

                                    
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.contest.show', $row->id) }}" class="btn btn-outline-info btn-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($row->status == 0)
                                <form action="{{ route('admin.contest.approve', $row->id) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm btn-success" title="Click to Approve"><i class="fas fa-check"></i></button>
                                </form>

                                <form action="{{ route('admin.contest.reject', $row->id) }}" method="POST" class="d-inline">
                                    @csrf @method('PUT')
                                    <button class="btn btn-sm btn-danger" title="Click to Reject"><i class="fas fa-times"></i></button>
                                </form>
                            @endif
                                </td>
                            </tr>
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $contest->links('pagination::bootstrap-5') }}
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
