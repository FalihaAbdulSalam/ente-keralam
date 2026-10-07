@extends('admin.layouts.app')

@section('title', isset($article) ? 'Edit Article' : 'Create Article')

@section('content')
<div class="container-fluid mb-4">
    <h2 class="fw-bold mb-4">
        {{ isset($article) ? 'Edit Article' : 'Create New Article' }}
    </h2>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ isset($article) ? 'Update Article Details' : 'Add New Article' }}</h5>
        </div>

        <div class="card-body">
            <form 
                action="{{ isset($article) ? route('admin.articles.update', $article->id) : route('admin.articles.store') }}" 
                method="POST" 
                enctype="multipart/form-data">
                @csrf
                @if(isset($article))
                    @method('PUT')
                @endif

                {{-- ✅ Article Type & Section --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Article Type</label>
                        <select name="articletype_id" class="form-control">
                            <option value="">Select Article Type</option>
                            @foreach($types as $type)
                                <option value="{{ $type->id }}" {{ old('articletype_id', $article->articletype_id ?? '') == $type->id ? 'selected' : '' }}>
                                    {{ $type->entitle }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Section</label>
                        <select name="sector_details_id" class="form-control">
                            <option value="">Select Section</option>
                            @foreach($sections as $section)
                                <option value="{{ $section->id }}" {{ old('sector_details_id', $article->sector_details_id ?? '') == $section->id ? 'selected' : '' }}>
                                    {{ $section->entitle }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- ✅ Titles --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title (English)</label>
                        <input type="text" name="entitle" class="form-control"
                            value="{{ old('entitle', $article->entitle ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title (Malayalam)</label>
                        <input type="text" name="maltitle" class="form-control"
                            value="{{ old('maltitle', $article->maltitle ?? '') }}">
                    </div>
                </div>

                {{-- ✅ Descriptions with CKEditor --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Description (English)</label>
                        <textarea  maxlength="300" name="endescription" id="endescription" class="form-control ckeditor" rows="2">{{ old('endescription', $article->endescription ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Description (Malayalam)</label>
                        <textarea maxlength="300" name="maldescription" id="maldescription" class="form-control ckeditor" rows="2">{{ old('maldescription', $article->maldescription ?? '') }}</textarea>
                    </div>
                </div>

                {{-- ✅ Contents with CKEditor --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Contents (English)</label>
                        <textarea name="encontent" id="encontent" class="form-control ckeditor" rows="6">{{ old('encontent', $article->encontent ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">content (Malayalam)</label>
                        <textarea name="malcontent" id="malcontent" class="form-control ckeditor" rows="6">{{ old('malcontent', $article->malcontent ?? '') }}</textarea>
                    </div>
                </div>

                {{-- ✅ Uploads --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Poster</label>
                        <input type="file" name="poster" class="form-control">
                        @if(isset($article->poster))
                            <div class="mt-2">
                                <img src="{{ asset($article->poster) }}" width="100" class="rounded border">
                                <small class="text-muted d-block mt-1">Current Poster</small>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Banner</label>
                        <input type="file" name="banner" class="form-control">
                        @if(isset($article->banner))
                            <div class="mt-2">
                                <img src="{{ asset($article->banner) }}" width="100" class="rounded border">
                                <small class="text-muted d-block mt-1">Current Banner</small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ✅ Status --}}
                <div class="mb-3 col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status', $article->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $article->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- ✅ Keywords / Tags (Select2 tags, saved as comma-separated) --}}
                @php
    // when editing article
    $selectedTags = isset($article->keywords)
        ? explode(',', $article->keywords)
        : [];
@endphp

<div class="row mb-3">
    <div class="col-md-12">
        <label class="form-label fw-semibold">Keywords (tags)</label>

       @php
    $selectedTags = json_decode($article->keywords, true) ?? [];
@endphp

<select id="keywords_select" name="keywords[]" class="form-control" multiple>
    @foreach($selectedTags as $tag)
        <option value="{{ $tag }}" selected>{{ $tag }}</option>
    @endforeach
</select>


        <small class="text-muted d-block mt-1">
            Keywords are stored as comma-separated text.
            Press <b>Enter</b> or <b>Comma</b> to add new tags.
        </small>
    </div>
</div>


                {{-- ✅ Buttons --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        {{ isset($article) ? 'Update' : 'Create' }}
                    </button>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


{{-- ✅ CKEditor Integration --}}
@push('scripts')

<!-- Select2 (jQuery + Select2 via CDN) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

    const __token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    document.querySelectorAll('.ckeditor').forEach(el => {
        ClassicEditor
            .create(el, {
                ckfinder: {
                    uploadUrl: "{{ route('admin.ckeditor.upload2') }}?_token=" + __token
                },
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'link', '|',
                    'bulletedList', 'numberedList', '|',
                    'blockQuote', 'insertTable', '|',
                    'imageUpload', '|',
                    'undo', 'redo'
                ]
            })
            .then(editor => {
                console.log('CKEditor initialized', editor);
            })
            .catch(error => {
                console.error('CKEditor init error:', error);
            });
    });

});
</script>
<script>
    (function () {
        $(function () {
            const $select = $('#keywords_select');
            const $hidden = $('#keywords');
            const existing = ($hidden.val() || '').trim();
            let initial = [];
            if (existing) initial = existing.split(',').map(s => s.trim()).filter(Boolean);

            // populate initial options
            initial.forEach(function (tag) {
                const option = new Option(tag, tag, true, true);
                $select.append(option);
            });

            $select.select2({
                tags: true,
                tokenSeparators: [','],
                width: '100%',
                placeholder: 'Add keywords'
            });

            // keep hidden input synced as comma-separated string on submit
            $('form').on('submit', function () {
                const vals = $select.val() || [];
                $hidden.val(vals.join(','));
            });
        });
    })();
</script>
@endpush
