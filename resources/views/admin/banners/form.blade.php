@extends('admin.layouts.app')

@section('title', isset($banner) ? 'Edit Banner' : 'Create New Banner')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">{{ isset($banner) ? 'Edit Banner' : 'Create New Banner' }}</h1>

            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="p-3 bg-primary text-white">Add New Banner</div>
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

                        <form action="{{ isset($banner) ? route('admin.banners.update', $banner->id) : route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if(isset($banner)) @method('PUT') @endif

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="entitle">English Title</label>
                                        <input type="text" name="entitle" id="entitle" class="form-control" value="{{ old('entitle', isset($banner) ? $banner->entitle : '') }}" required maxlength="255">
                                    </div>

                                    <div class="form-group">
                                        <label for="maltitle">Malayalam Title</label>
                                        <input type="text" name="maltitle" id="maltitle" class="form-control" value="{{ old('maltitle', isset($banner) ? $banner->maltitle : '') }}" required maxlength="255">
                                    </div>

                                    <div class="form-group">
                                        <label for="endescription">English Description</label>
                                        <textarea name="endescription" id="endescription" class="form-control" rows="4">{{ old('endescription', isset($banner) ? $banner->endescription : '') }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="maldescription">Malayalam Description</label>
                                        <textarea name="maldescription" id="maldescription" class="form-control" rows="4">{{ old('maldescription', isset($banner) ? $banner->maldescription : '') }}</textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="poster">Poster (English) (jpg,png,webp max 10MB)</label>
                                        @if(isset($banner) && $banner->poster)
                                            <div class="mb-2"><img src="{{ asset('uploads/' . $banner->poster) }}" alt="poster" style="max-width:180px; max-height:120px; display:block;"></div>
                                        @endif
                                        <div class="custom-file mb-1">
                                            <input type="file" class="custom-file-input" id="poster" name="poster" accept="image/png,image/jpeg,image/webp">
                                            <label class="custom-file-label" for="poster">Choose file</label>
                                        </div>
                                        <small class="form-text text-muted">Allowed: JPG, PNG, WEBP</small>
                                        <div id="poster-preview" class="mt-2"></div>
                                    </div>

                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="malposter">Poster (Malayalam) (optional)</label>
                                        @if(isset($banner) && $banner->malposter)
                                            <div class="mb-2"><img src="{{ asset('uploads/' . $banner->malposter) }}" alt="malposter" style="max-width:180px; max-height:120px; display:block;"></div>
                                        @endif
                                        <div class="custom-file mb-1">
                                            <input type="file" class="custom-file-input" id="malposter" name="malposter" accept="image/png,image/jpeg,image/webp">
                                            <label class="custom-file-label" for="malposter">Choose file</label>
                                        </div>
                                        <small class="form-text text-muted">Optional — JPG, PNG, WEBP</small>
                                        <div id="malposter-preview" class="mt-2"></div>
                                    </div>

                                    <div class="form-group">
                                        <label for="youtubelink">YouTube Link (optional)</label>
                                        <input type="url" name="youtubelink" id="youtubelink" class="form-control" value="{{ old('youtubelink', isset($banner) ? $banner->youtubelink : '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="link">External Link (optional)</label>
                                        <input type="url" name="link" id="link" class="form-control" value="{{ old('link', isset($banner) ? $banner->link : '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="link_text">Link Text (optional)</label>
                                        <input type="text" name="link_text" id="link_text" class="form-control" value="{{ old('link_text', isset($banner) ? $banner->link_text : '') }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="banercategory_id">Banner Category</label>
                                        <select name="banercategory_id" id="banercategory_id" class="form-control" required>
                                            <option value="">-- Select Category --</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('banercategory_id', isset($banner) ? $banner->banercategory_id : '') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="order_num">Order Number</label>
                                        <input type="number" name="order_num" id="order_num" class="form-control" value="{{ old('order_num', isset($banner) ? $banner->order_num : 0) }}">
                                    </div>

                                    <div class="form-group">
                                        <label for="status">Status</label>
                                        <select name="status" id="status" class="form-control">
                                            <option value="1" {{ old('status', isset($banner) ? $banner->status : 1) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status', isset($banner) ? $banner->status : 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="form-group mt-4">
                                        <button type="submit" class="btn btn-success">{{ isset($banner) ? 'Update' : 'Create' }}</button>
                                        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Cancel</a>
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
document.addEventListener('DOMContentLoaded', function(){
    function initFileInput(id, previewId){
        const input = document.getElementById(id);
        if(!input) return;
        input.addEventListener('change', function(e){
            const label = this.nextElementSibling;
            if(label) label.innerText = this.files && this.files[0] ? this.files[0].name : 'Choose file';
            const preview = document.getElementById(previewId);
            preview.innerHTML = '';
            const file = this.files && this.files[0];
            if(!file) return;
            if(file.size > 10 * 1024 * 1024){
                preview.innerHTML = '<div class="text-danger">Selected file is larger than 10MB.</div>';
                this.value = '';
                if(label) label.innerText = 'Choose file';
                return;
            }
            const img = document.createElement('img');
            img.style.maxWidth = '180px'; img.style.maxHeight = '120px';
            img.src = URL.createObjectURL(file);
            preview.appendChild(img);
        });
    }
    initFileInput('poster','poster-preview');
    initFileInput('malposter','malposter-preview');
});
</script>
@endpush

@endsection
