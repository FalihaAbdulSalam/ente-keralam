@extends('admin.layouts.app')

@section('title', isset($sector_detail) ? 'Edit Counter' : 'Create Counter')

@section('content')


<div class="container-fluid mb-4">
    <h2 class="fw-bold mb-4">
        {{ isset($sector_detail) ? 'Edit Counter' : 'Create New Counter' }}
    </h2>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ isset($sector_detail) ? 'Update Counter Details' : 'Add New Counter' }}</h5>
        </div>

        <div class="card-body">
            <form 
                action="{{ isset($counter) ? route('admin.counter_details.update', $counter->id) : route('admin.counter_details.store') }}" 
                method="POST"
                enctype="multipart/form-data"
            >
                @csrf
                @if(isset($counter))
                    @method('PUT')
                @endif

                {{-- ✅ English & Malayalam Titles --}}
              <div class="row mb-3">
    <!-- Sector ID -->
    <div class="col-md-6">
        <label for="sector_id" class="form-label fw-semibold">Select Sector</label>
        <select name="sector_id" id="sector_id" class="form-control" required>
            <option value="">-- Select Sector --</option>
            @foreach($sectors as $sector)
                <option value="{{ $sector->id }}"
                    {{ old('sector_id', $counter->sector_id ?? '') == $sector->id ? 'selected' : '' }}>
                    {{ $sector->entitle }}
                </option>
            @endforeach
        </select>
    </div>

    <!-- Counter Number -->
    <div class="col-md-6">
        <label for="counter_number" class="form-label fw-semibold">Counter Number</label>
        <input type="text" name="counter_number" id="counter_number" class="form-control"
               value="{{ old('counter_number', $counter->counter_number ?? '') }}" required>
    </div>
</div>

<div class="row mb-3">
    <!-- Counter Title -->
    <div class="col-md-6">
        <label for="title" class="form-label fw-semibold">Counter Title</label>
        <input type="text" name="entitle" id="entitle" class="form-control"
               value="{{ old('entitle', $counter->entitle ?? '') }}" required>
    </div>

     <!-- Counter Title -->
    <div class="col-md-6">
        <label for="title" class="form-label fw-semibold">Counter Malayalam Title</label>
        <input type="text" name="maltitle" id="maltitle" class="form-control"
               value="{{ old('maltitle', $counter->maltitle ?? '') }}" required>
    </div>

    <!-- Numeric Type Icon -->
    <div class="col-md-6 mt-2 mb-2">
        <label for="numeric_type_icon" class="form-label fw-semibold">Numeric Type Icon</label>
        <input type="text" name="numeric_type_icon" id="numeric_type_icon" class="form-control"
               value="{{ old('numeric_type_icon', $counter->numeric_type_icon ?? '') }}">
        <small class="text-muted">Example: fas fa-chart-line</small>
    </div>
</div>

<div class="row mb-3">
    <!-- Main Icon -->
    <div class="col-md-6">
        <div class="row mb-3">
    <!-- Font Awesome Icon -->
    <div class="col-md-6">
        <label class="form-label fw-semibold">Font Awesome Icon (Optional)</label>
        <input type="text" name="icon" id="icon" class="form-control"
               value="{{ old('icon', $counter->icon ?? '') }}"
               placeholder="fas fa-tree">
        <small class="text-muted">Example: <code>fas fa-tree</code></small>
    </div>

    <!-- Image Upload -->
    <div class="col-md-6">
        <label class="form-label fw-semibold">Upload Icon Image (Optional)</label>
        <input type="file" name="icon_file" class="form-control" accept="image/*">

        @if(!empty($counter->icon))
            <div class="mt-2">
                <img src="{{ asset($counter->icon) }}"
                     width="60" height="60" class="border rounded">
            </div>
        @endif
        <small class="text-muted">Allowed: PNG, JPG, SVG</small>
    </div>
</div>

    </div>

    <!-- Status -->
    <div class="col-md-6">
        <label for="status" class="form-label fw-semibold">Status</label>
        <select name="status" id="status" class="form-control">
            <option value="1" {{ old('status', $counter->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('status', $counter->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
</div>

<div class="text-end">
    <button type="submit" class="btn btn-success px-4">
        {{ isset($counter) ? 'Update Counter' : 'Create Counter' }}
    </button>
    <a href="{{ route('admin.counter_details.index') }}" class="btn btn-secondary px-4">Cancel</a>
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
