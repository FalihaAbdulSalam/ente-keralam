@extends('admin.layouts.app')

@section('title', 'Manage Creative Thoughts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body">
                    <h2 class="mb-0">Manage Creative Thoughts</h2>
                    <p class="text-muted">Add, edit, and manage bilingual creative thought entries</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Creative Thoughts List</h5>
                    <a href="{{ route('admin.creativethoughts.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Add New Creative Thought</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th style="width:60px;">#</th>
                                    <th>English Title</th>
                                    <th>Malayalam Title</th>
                                    <th style="width:140px;">Poster</th>
                                    <th style="width:90px;">Status</th>
                                    <th style="width:140px;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($creativethoughts as $index => $item)
                                    <tr>
                                        <td>{{ $creativethoughts->firstItem() + $index }}</td>
                                        <td>{{ $item->entitle }}</td>
                                        <td>{{ $item->maltitle }}</td>
                                        <td>
                                            @if($item->poster)
                                                <img src="{{ asset('uploads/' . $item->poster) }}" alt="poster" style="max-width:100px; max-height:60px;">
                                            @endif
                                        </td>
                                        <td>
                                            @if($item->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.creativethoughts.edit', $item->id) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa fa-edit"></i></a>
                                            <form action="{{ route('admin.creativethoughts.destroy', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fa fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-3">
                        {{ $creativethoughts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
