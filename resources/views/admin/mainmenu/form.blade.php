@extends('admin.layouts.app')

@section('title', isset($mainmenu) ? 'Edit Main Menu' : 'Create Main Menu')

@section('content')
<div class="container-fluid mb-4">
    <h2 class="fw-bold mb-4">
        {{ isset($mainmenu) ? 'Edit Main Menu' : 'Create New Main Menu' }}
    </h2>

    {{-- ✅ Form Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ isset($mainmenu) ? 'Update Menu Details' : 'Add New Menu' }}</h5>
        </div>

        <div class="card-body">
            <form 
                action="{{ isset($mainmenu) ? route('admin.mainmenu.update', $mainmenu->id) : route('admin.mainmenu.store') }}" 
                method="POST"
            >
                @csrf
                @if(isset($mainmenu))
                    @method('PUT')
                @endif

                {{-- ✅ Row 1: English + Malayalam Names --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="entitle" class="form-label fw-semibold">Menu Name (English)</label>
                        <input 
                            type="text" 
                            name="entitle" 
                            id="entitle" 
                            class="form-control" 
                            placeholder="Enter menu name in English"
                            value="{{ old('entitle', $mainmenu->entitle ?? '') }}" 
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="maltitle" class="form-label fw-semibold">Menu Name (Malayalam)</label>
                        <input 
                            type="text" 
                            name="maltitle" 
                            id="maltitle" 
                            class="form-control" 
                            placeholder="Enter menu name in Malayalam"
                            value="{{ old('maltitle', $mainmenu->maltitle ?? '') }}"
                        >
                    </div>
                </div>

                {{-- ✅ Row 2: Slug + Order --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-semibold">Slug</label>
                        <input 
                            type="text" 
                            name="slug" 
                            id="slug" 
                            class="form-control" 
                            placeholder="Auto-generated from English name or custom"
                            value="{{ old('slug', $mainmenu->slug ?? '') }}"
                        >
                        <small class="text-muted">Example: "about-us" or "home"</small>
                    </div>

                    <div class="col-md-6">
                        <label for="order" class="form-label fw-semibold">Order</label>
                        <input 
                            type="number" 
                            name="order" 
                            id="order" 
                            class="form-control" 
                            min="0"
                            placeholder="Display order (0 for top)"
                            value="{{ old('order', $mainmenu->order ?? 0) }}"
                        >
                    </div>
                </div>

                {{-- ✅ Row 3: Status --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ old('status', $mainmenu->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $mainmenu->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                {{-- ✅ Submit Buttons --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        {{ isset($menu) ? 'Update Menu' : 'Create Menu' }}
                    </button>
                    <a href="{{ route('admin.mainmenu.index') }}" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>


@endsection
