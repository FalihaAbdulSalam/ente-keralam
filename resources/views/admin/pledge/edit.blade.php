@extends('admin.layouts.app')

@section('title', 'Edit Pledge')

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

    .preview-img {
        max-width: 180px;
        border-radius: 8px;
        border: 1px solid #eee;
        padding: 5px;
        margin-top: 5px;
    }
</style>
@endpush

@section('content')
<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">
    <div class="d-flex justify-content-between mb-3">
        <h4>Edit Pledge</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.pledge.update', $pledge->pledge_id) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="pledge_title" class="form-control" value="{{ $pledge->pledge_title }}" required>
        </div>

        <div class="mb-3">
            <label>Description</label>
            <textarea name="pledge_description" class="form-control" rows="3">{{ $pledge->pledge_description }}</textarea>
        </div>

        <div class="mb-3">
            <label>Content (Malayalam / English)</label>
            <textarea name="pledge_content" id="editor" class="form-control" rows="8">{!! $pledge->pledge_content !!}</textarea>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Start Date</label>
                <input type="date" name="pledge_startDate" class="form-control" value="{{ $pledge->pledge_startDate }}">
            </div>
            <div class="col-md-6 mb-3">
                <label>End Date</label>
                <input type="date" name="pledge_endDate" class="form-control" value="{{ $pledge->pledge_endDate }}">
            </div>
        </div>

        <div class="mb-3">
            <label>Score</label>
            <input type="text" name="pledge_score" class="form-control" value="{{ $pledge->pledge_score }}" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Banner (JPG/PNG, max 2MB)</label>
                <input type="file" name="banner" accept=".jpg,.jpeg,.png" class="form-control">
                @if($pledge->banner)
                    <div>
                        <small class="text-muted">Current Banner:</small><br>
                        <img src="{{ asset('storage/' . $pledge->banner) }}" class="preview-img" alt="Banner">
                    </div>
                @endif
            </div>

            <div class="col-md-6 mb-3">
                <label>Poster (JPG/PNG, max 2MB)</label>
                <input type="file" name="poster" accept=".jpg,.jpeg,.png" class="form-control">
                @if($pledge->poster)
                    <div>
                        <small class="text-muted">Current Poster:</small><br>
                        <img src="{{ asset('storage/' . $pledge->poster) }}" class="preview-img" alt="Poster">
                    </div>
                @endif
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.pledge') }}" class="btn btn-secondary">Cancel</a>
    </form>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#editor'), {
                language: 'en',
                toolbar: [
                    'undo', 'redo', '|',
                    'bold', 'italic', 'underline', '|',
                    'bulletedList', 'numberedList', '|',
                    'alignment', '|',
                    'link', 'blockQuote', 'insertTable'
                ]
            })
            .then(editor => console.log('CKEditor initialized', editor))
            .catch(error => console.error('CKEditor error:', error));
    </script>
</div>
@endsection
