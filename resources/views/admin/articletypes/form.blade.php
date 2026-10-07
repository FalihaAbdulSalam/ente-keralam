@extends('admin.layouts.app')

@section('title', isset($articletype) ? 'Edit Article Type' : 'Create Article Type')

@section('content')
<div class="container-fluid mb-4">
    <h2 class="fw-bold mb-4">
        {{ isset($articletype) ? 'Edit Article Type' : 'Create New Article Type' }}
    </h2>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ isset($articletype) ? 'Update Article Type' : 'Add New Article Type' }}</h5>
        </div>

        <div class="card-body">
            <form 
                action="{{ isset($articletype) ? route('admin.articletypes.update', $articletype->id) : route('admin.articletypes.store') }}" 
                method="POST">
                @csrf
                @if(isset($articletype))
                    @method('PUT')
                @endif

                {{-- Titles --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title (English)</label>
                        <input type="text" name="entitle" class="form-control"
                            value="{{ old('entitle', $articletype->entitle ?? '') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Title (Malayalam)</label>
                        <input type="text" name="maltitle" class="form-control"
                            value="{{ old('maltitle', $articletype->maltitle ?? '') }}">
                    </div>
                </div>

              
                {{-- Status --}}
                <div class="mb-3 col-md-3">
                    <label class="form-label fw-semibold">Status</label>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status', $articletype->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $articletype->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                {{-- Buttons --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        {{ isset($articletype) ? 'Update' : 'Create' }}
                    </button>
                    <a href="{{ route('admin.articletypes.index') }}" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
