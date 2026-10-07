@extends('admin.layouts.app')

@section('title', isset($footer) ? 'Edit Footer' : 'Create New Footer')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">{{ isset($footer) ? 'Edit Footer' : 'Create New Footer' }}</h1>

            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="p-3 bg-primary text-white">Add New Footer</div>
                    <div class="p-4">

                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ isset($footer) ? route('admin.footers.update', $footer->id) : route('admin.footers.store') }}" method="POST">
                            @csrf
                            @if(isset($footer)) @method('PUT') @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="entitle">English Title</label>
                                        <input type="text" name="entitle" id="entitle" class="form-control" value="{{ old('entitle', isset($footer) ? $footer->entitle : '') }}" required maxlength="255">
                                    </div>

                                    <div class="form-group">
                                        <label for="maltitle">Malayalam Title</label>
                                        <input type="text" name="maltitle" id="maltitle" class="form-control" value="{{ old('maltitle', isset($footer) ? $footer->maltitle : '') }}" required maxlength="255">
                                    </div>

                                    <div class="form-group">
                                        <label for="link">Link (optional)</label>
                                        <input type="url" name="link" id="link" class="form-control" value="{{ old('link', isset($footer) ? $footer->link : '') }}">
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="link_text">Link Text (optional)</label>
                                        <input type="text" name="link_text" id="link_text" class="form-control" value="{{ old('link_text', isset($footer) ? $footer->link_text : '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="footercategory_id">Footer Category</label>
                                        <select name="footercategory_id" id="footercategory_id" class="form-control" required>
                                            <option value="">-- Select Category --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('footercategory_id', isset($footer) ? $footer->footercategory_id : '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="order_num">Order Number</label>
                                        <input type="number" name="order_num" id="order_num" class="form-control" value="{{ old('order_num', isset($footer) ? $footer->order_num : 0) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" {{ old('status', isset($footer) ? $footer->status : 1) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status', isset($footer) ? $footer->status : 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-success">{{ isset($footer) ? 'Update' : 'Create' }}</button>
                                        <a href="{{ route('admin.footers.index') }}" class="btn btn-secondary">Cancel</a>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
