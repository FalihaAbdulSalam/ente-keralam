@extends('admin.layouts.app')

@section('title', 'Manage Articles')

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

    .article-img {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 6px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h1 class="page-title">Manage Articles</h1>
    <p class="page-subtitle">Create and manage bilingual article entries</p>
</div>

<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">

    {{-- ✅ Flash Messages --}}
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
            <h5 class="mb-0">Article List</h5>
            <a href="{{ route('admin.articles.create') }}" class="btn btn-sm bg-primary text-white">
                <i class="fas fa-plus-circle me-1"></i> Add New Article
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="articleTable" class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Title (English)</th>
                            <th>Title (Malayalam)</th>
                            <th>Type</th>
                            <th>Sector</th>
                            <th>Poster</th>
                            <th>Status</th>
                            <th class="text-center" width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $article->entitle }}</td>
                                <td>{{ $article->maltitle ?? '—' }}</td>
                                <td>{{ $article->type->entitle ?? '—' }}</td>
                                <td>{{ $article->sector->entitle ?? '—' }}</td>
                                <td>
                                    @if($article->poster)
                                        <img src="{{ asset($article->poster) }}" class="article-img" alt="Poster">
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($article->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.articles.edit', $article->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to delete this article?')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">No articles found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- ✅ Optional Pagination --}}
            @if(method_exists($articles, 'links'))
                <div class="mt-3">
                    {{ $articles->links('pagination::bootstrap-5') }}
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
    $('#articleTable').DataTable({
        "pageLength": 10,
        "ordering": true,
        "searching": true,
        "info": true,
        "responsive": true,
        "autoWidth": false,
        "language": {
            "search": "🔍 Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ articles",
            "zeroRecords": "No matching articles found"
        }
    });
});
</script>
@endsection
