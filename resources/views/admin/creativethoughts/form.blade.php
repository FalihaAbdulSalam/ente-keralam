@extends('admin.layouts.app')

@section('title', isset($creativethought) ? 'Edit Creative Thought' : 'Create New Creative Thought')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">{{ isset($creativethought) ? 'Edit Creative Thought' : 'Create New Creative Thought' }}</h1>

            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="p-3 bg-primary text-white">Add New Creative Thought</div>
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

                        <form action="{{ isset($creativethought) ? route('admin.creativethoughts.update', $creativethought->id) : route('admin.creativethoughts.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($creativethought)) @method('PUT') @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="entitle">English Title</label>
                                        <input type="text" name="entitle" id="entitle" class="form-control" value="{{ old('entitle', isset($creativethought) ? $creativethought->entitle : '') }}" required maxlength="255">
                                    </div>

                                    <div class="form-group">
                                        <label for="maltitle">Malayalam Title</label>
                                        <input type="text" name="maltitle" id="maltitle" class="form-control" value="{{ old('maltitle', isset($creativethought) ? $creativethought->maltitle : '') }}" required maxlength="255">
                                    </div>

                                    <div class="form-group">
                                        <label for="poster">Upload Poster (jpg, png, webp) <small class="text-muted">(max 10MB)</small></label>
                                        @if(isset($creativethought) && $creativethought->poster)
                                            <div class="mb-2">
                                                <img src="{{ asset('uploads/' . $creativethought->poster) }}" alt="poster" style="max-width:180px; max-height:120px; display:block;">
                                            </div>
                                        @endif
                                        <div class="custom-file mb-1">
                                            <input type="file" class="custom-file-input" id="poster" name="poster" accept="image/png,image/jpeg,image/webp">
                                            <label class="custom-file-label" for="poster">Choose file</label>
                                        </div>
                                        <small class="form-text text-muted">Allowed: JPG, PNG, WEBP. Leave empty to keep existing poster.</small>
                                        <div id="poster-preview" class="mt-2"></div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="order_num">Order Number</label>
                                        <input type="number" name="order_num" id="order_num" class="form-control" value="{{ old('order_num', isset($creativethought) ? $creativethought->order_num : '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" {{ old('status', isset($creativethought) ? $creativethought->status : 1) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status', isset($creativethought) ? $creativethought->status : 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                        </div>
                                    </div>

                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-success">{{ isset($creativethought) ? 'Update' : 'Create' }}</button>
                                        <a href="{{ route('admin.creativethoughts.index') }}" class="btn btn-secondary">Cancel</a>
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

@push('scripts')
<script>
// Bootstrap custom file input label
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.custom-file-input').forEach(function(input){
        input.addEventListener('change', function(e){
            var fileName = e.target.files.length ? e.target.files[0].name : 'Choose file';
            var label = e.target.nextElementSibling;
            if(label) label.innerText = fileName;

            // preview and size validation
            const preview = document.getElementById('poster-preview');
            preview.innerHTML = '';
            const file = e.target.files && e.target.files[0];
            if(!file) return;
            if(file.size > 10 * 1024 * 1024) {
                preview.innerHTML = '<div class="text-danger">Selected file is larger than 10MB.</div>';
                e.target.value = '';
                if(label) label.innerText = 'Choose file';
                return;
            }
            const img = document.createElement('img');
            img.style.maxWidth = '180px';
            img.style.maxHeight = '120px';
            img.src = URL.createObjectURL(file);
            preview.appendChild(img);
        });
    });
});
</script>
@endpush

@endsection
