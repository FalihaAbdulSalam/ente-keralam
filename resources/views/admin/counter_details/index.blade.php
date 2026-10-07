@extends('admin.layouts.app')

@section('title', 'Manage Sectors')

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

    .table th {
        white-space: nowrap;
    }

    .badge {
        font-size: 0.85rem;
        padding: 6px 10px;
        border-radius: 6px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Manage Sectors</h1>
    <p class="page-subtitle">Add, edit, and manage bilingual sector details</p>
</div>

<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">

    {{-- ✅ Success / Error Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @elseif(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ✅ Main Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Counter Details List</h5>
            <a href="{{ route('admin.counter_details.create') }}" class="btn btn-sm bg-primary text-white">
                <i class="fas fa-plus-circle me-1"></i> Add New Counter Details
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="sectorTable" class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>English Title</th>
                            <th>Malayalam Title</th>
                            <th>Icon</th>
                            <th>Status</th>
                            <th class="text-center" width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sectors as $sector)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $sector->entitle }}</td>
                                <td>{{ $sector->maltitle ?? '—' }}</td>
                                <td>
                                    @if($sector->icon)
                                        @if(Str::startsWith($sector->icon, ['fa', 'fas', 'fab']))
                                            <i class="{{ $sector->icon }} fs-5"></i>
                                        @else
                                            <img src="{{ asset($sector->icon) }}" alt="icon" width="40" height="40" class="rounded">
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if($sector->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.counter_details.edit', $sector->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.counter_details.destroy', $sector->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this sector?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No sectors found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ✅ Optional pagination support --}}
            @if(method_exists($sectors, 'links'))
            <div class="mt-3">
                {{ $sectors->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

{{-- ✅ Scripts --}}
@section('scripts')
<script>
$(document).ready(function() {
    $('#sectorTable').DataTable({
        "pageLength": 10,
        "ordering": true,
        "searching": true,
        "info": true,
        "responsive": true,
        "autoWidth": false,
        "language": {
            "search": "🔍 Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ sectors",
            "zeroRecords": "No matching sectors found"
        }
    });
});
</script>
@endsection
