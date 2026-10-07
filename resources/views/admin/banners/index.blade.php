@extends('admin.layouts.app')

@section('title', 'Manage Banners')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="p-3 bg-primary text-white">Manage Banners</div>
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Banner List</h5>
                            <a href="{{ route('admin.banners.create') }}" class="btn btn-primary btn-sm">+ Add New Banner</a>
                        </div>

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
                                        <th style="width:160px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($banners as $i => $item)
                                        <tr>
                                            <td>{{ $banners->firstItem() + $i }}</td>
                                            <td>{{ $item->entitle }}</td>
                                            <td>{{ $item->maltitle }}</td>
                                            <td>
                                                @if($item->poster)
                                                    <img src="{{ asset('uploads/' . $item->poster) }}" alt="poster" style="max-width:120px; max-height:80px;">
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
                                                <a href="{{ route('admin.banners.edit', $item->id) }}" class="btn btn-sm btn-outline-primary"><i class="fa fa-edit"></i></a>
                                                <form action="{{ route('admin.banners.destroy', $item->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this banner?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">{{ $banners->links() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
