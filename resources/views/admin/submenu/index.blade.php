@extends('admin.layouts.app')

@section('title', 'Manage Sub Menus')

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

    table td small {
        color: #888;
    }

    .table > :not(caption) > * > * {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Manage Sub Menus</h1>
    <p class="page-subtitle">Add, edit, or remove bilingual submenu items under each main menu</p>
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
            <h5 class="mb-0">Submenu List</h5>
            <a href="{{ route('admin.submenu.create') }}" class="btn btn-sm bg-primary text-white">
                <i class="fas fa-plus-circle me-1"></i> Add New Submenu
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="submenuTable" class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Main Menu</th>
                            <th>Submenu (English)</th>
                            <th>Submenu (Malayalam)</th>
                            <th>Slug</th>
                            <th>Order</th>
                            <th>Status</th>
                            <th class="text-center" width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submenus as $submenu)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><strong>{{ $submenu->mainmenu->entitle ?? '-' }}</strong></td>
                                <td>{{ $submenu->entitle }}</td>
                                <td>{{ $submenu->maltitle ?? '—' }}</td>
                                <td><small>{{ $submenu->slug ?? '—' }}</small></td>
                                <td>{{ $submenu->order ?? 0 }}</td>
                                <td>
                                    @if($submenu->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.submenu.edit', $submenu->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.submenu.destroy', $submenu->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this submenu?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No submenu items found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ✅ Optional pagination --}}
            @if(method_exists($submenus, 'links'))
            <div class="mt-3">
                {{ $submenus->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

{{-- ✅ DataTables Scripts --}}
@section('scripts')
<script>
$(document).ready(function() {
    $('#submenuTable').DataTable({
        "pageLength": 10,
        "ordering": true,
        "searching": true,
        "info": true,
        "responsive": true,
        "autoWidth": false,
        "language": {
            "search": "🔍 Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ submenus",
            "zeroRecords": "No matching submenus found"
        },
        "columnDefs": [
            { "orderable": false, "targets": [7] } // Disable ordering for actions column
        ]
    });
});
</script>
@endsection
