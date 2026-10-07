@extends('admin.layouts.app')

@section('title', isset($submenu) ? 'Edit Submenu' : 'Create Submenu')

@section('content')
<div class="container-fluid mb-4">
    <h2 class="fw-bold mb-4">
        {{ isset($submenu) ? 'Edit Submenu' : 'Create New Submenu' }}
    </h2>

    {{-- ✅ Form Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ isset($submenu) ? 'Update Submenu Details' : 'Add New Submenu' }}</h5>
        </div>

        <div class="card-body">
            <form 
                action="{{ isset($submenu) ? route('admin.submenu.update', $submenu->id) : route('admin.submenu.store') }}" 
                method="POST"
            >
                @csrf
                @if(isset($submenu))
                    @method('PUT')
                @endif

                {{-- ✅ Row 1: Parent Menu + Order --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="main_menu_id" class="form-label fw-semibold">Parent Main Menu</label>
                        <select name="main_menu_id" id="main_menu_id" class="form-control" required>
                            <option value="">-- Select Main Menu --</option>
                            @foreach($mainmenus as $menu)
                                <option value="{{ $menu->id }}" 
                                    {{ old('main_menu_id', $submenu->main_menu_id ?? '') == $menu->id ? 'selected' : '' }}>
                                    {{ $menu->entitle }}
                                </option>
                            @endforeach
                        </select>
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
                            value="{{ old('order', $submenu->order ?? 0) }}"
                        >
                    </div>
                </div>

                {{-- ✅ Row 2: English + Malayalam Titles --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="entitle" class="form-label fw-semibold">Submenu Name (English)</label>
                        <input 
                            type="text" 
                            name="entitle" 
                            id="entitle" 
                            class="form-control" 
                            placeholder="Enter submenu name in English"
                            value="{{ old('entitle', $submenu->entitle ?? '') }}" 
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="maltitle" class="form-label fw-semibold">Submenu Name (Malayalam)</label>
                        <input 
                            type="text" 
                            name="maltitle" 
                            id="maltitle" 
                            class="form-control" 
                            placeholder="Enter submenu name in Malayalam"
                            value="{{ old('maltitle', $submenu->maltitle ?? '') }}"
                        >
                    </div>
                </div>

                {{-- ✅ Row 3: Slug --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="slug" class="form-label fw-semibold">Slug</label>
                        <input 
                            type="text" 
                            name="slug" 
                            id="slug" 
                            class="form-control" 
                            placeholder="Auto-generated from English name or custom"
                            value="{{ old('slug', $submenu->slug ?? '') }}"
                        >
                        <small class="text-muted">Example: "events-gallery" or "downloads"</small>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="1" {{ old('status', $submenu->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $submenu->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                {{-- ✅ Submit Buttons --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        {{ isset($submenu) ? 'Update Submenu' : 'Create Submenu' }}
                    </button>
                    <a href="{{ route('admin.submenu.index') }}" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
