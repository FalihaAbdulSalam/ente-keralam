@extends('admin.layouts.app')

@section('title', 'Manage Article Types')

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
    <h1 class="page-title">Manage Article Types</h1>
    <p class="page-subtitle">Create and manage bilingual article type entries</p>
</div>

<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">

    {{-- ✅ Success / Error Message --}}
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
            <h5 class="mb-0">Article Type List</h5>
            <a href="{{ route('admin.articletypes.create') }}" class="btn btn-sm bg-primary text-white">
                <i class="fas fa-plus-circle me-1"></i> Add New Article Type
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="articleTypeTable" class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Type (English)</th>
                            <th>Type (Malayalam)</th>
                            <th>Description (English)</th>
                            <th>Status</th>
                            <th class="text-center" width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articletypes as $type)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $type->entitle }}</td>
                                <td>{{ $type->maltitle ?? '—' }}</td>
                                <td>{!! Str::limit(strip_tags($type->endescription), 50) !!}</td>
                                <td>
                                    @if($type->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.articletypes.edit', $type->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.articletypes.destroy', $type->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Are you sure you want to delete this article type?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No article types found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ✅ Optional pagination if not using DataTable --}}
            @if(method_exists($articletypes, 'links'))
                <div class="mt-3">
                    {{ $articletypes->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

{{-- ✅ DataTable Script --}}
@section('scripts')
<script>
$(document).ready(function() {
    $('#articleTypeTable').DataTable({
        "pageLength": 10,
        "ordering": true,
        "searching": true,
        "info": true,
        "responsive": true,
        "autoWidth": false,
        "language": {
            "search": "🔍 Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ article types",
            "zeroRecords": "No matching records found"
        }
    });
});
</script>
@endsection
