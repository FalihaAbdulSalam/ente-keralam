@extends('admin.layouts.app')

@section('title', isset($sector_detail) ? 'Edit Sector' : 'Create Sector')

@section('content')
<div class="container-fluid mb-4">
    <h2 class="fw-bold mb-4">
        {{ isset($sector_detail) ? 'Edit Sector' : 'Create New Sector' }}
    </h2>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ isset($sector_detail) ? 'Update Sector Details' : 'Add New Sector' }}</h5>
        </div>

        <div class="card-body">
            <form 
                action="{{ isset($sector_detail) ? route('admin.sector_details.update', $sector_detail->id) : route('admin.sector_details.store') }}" 
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                @if(isset($sector_detail))
                    @method('PUT')
                @endif

                {{-- ✅ English & Malayalam Titles --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="entitle" class="form-label fw-semibold">Sector Title (English)</label>
                        <input type="text" name="entitle" id="entitle" class="form-control" 
                            value="{{ old('entitle', $sector_detail->entitle ?? '') }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="maltitle" class="form-label fw-semibold">Sector Title (Malayalam)</label>
                        <input type="text" name="maltitle" id="maltitle" class="form-control"
                            value="{{ old('maltitle', $sector_detail->maltitle ?? '') }}">
                    </div>
                </div>

                {{-- ✅ Descriptions with CKEditor --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="endescription" class="form-label fw-semibold">Description (English)</label>
                        <textarea name="endescription" id="endescription" class="form-control ckeditor" rows="6">{{ old('endescription', $sector_detail->endescription ?? '') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="maldescription" class="form-label fw-semibold">Description (Malayalam)</label>
                        <textarea name="maldescription" id="maldescription" class="form-control ckeditor" rows="6">{{ old('maldescription', $sector_detail->maldescription ?? '') }}</textarea>
                    </div>
                </div>

                {{-- ✅ Icon + Status --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="icon" class="form-label fw-semibold">Icon (Font Awesome or Image)</label>
                        <input type="text" name="icon" id="icon" class="form-control" 
                            value="{{ old('icon', $sector_detail->icon ?? '') }}">
                        <small class="text-muted">Example: <code>fas fa-tree</code></small>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ old('status', $sector_detail->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $sector_detail->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                {{-- ✅ Poster Upload --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Upload Icon (Optional)</label>
                        <input type="file" name="rupee_icon" class="form-control" accept="image/*">

                        @if(!empty($sector_detail->rupee_icon ?? null))
                            <div class="mt-2">
                                <img src="{{ asset('uploads/rupee_icon/' . $sector_detail->rupee_icon) }}" 
                                     width="120" class="border rounded">
                            </div>
                        @endif
                        <small class="text-muted">Allowed: PNG, JPG, SVG</small>
                    </div>
                </div>

                {{-- ✅ Poster Upload --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Upload Poster (Optional)</label>
                        <input type="file" name="poster" class="form-control" accept="image/*">

                        @if(!empty($sector_detail->poster ?? null))
                            <div class="mt-2">
                                <img src="{{ asset('uploads/posters/' . $sector_detail->poster) }}" 
                                     width="120" class="border rounded">
                            </div>
                        @endif
                        <small class="text-muted">Allowed: PNG, JPG, SVG</small>
                    </div>
                </div>

              
                {{-- ✅ Submit Buttons --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        {{ isset($sector_detail) ? 'Update Sector' : 'Create Sector' }}
                    </button>
                    <a href="{{ route('admin.sector_details.index') }}" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {{-- ✅ Load CKEditor with Upload Plugin --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const editors = document.querySelectorAll('.ckeditor');
            editors.forEach(el => {
                ClassicEditor
                    .create(el, {
                        ckfinder: {
                            uploadUrl: "{{ route('admin.ckeditor.upload').'?_token='.csrf_token() }}"
                        },
                        toolbar: [
                            'heading', '|',
                            'bold', 'italic', 'link', '|',
                            'bulletedList', 'numberedList', '|',
                            'insertTable', 'blockQuote', '|',
                            'imageUpload', 'undo', 'redo'
                        ]
                    })
                    .catch(error => console.error(error));
            });
        });
    </script>
@endpush
