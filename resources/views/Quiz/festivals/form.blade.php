@extends('layouts.quizadmin')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ isset($festival) ? 'Edit Festival' : 'Create Festival' }}</h2>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <form 
                action="{{ isset($festival) ? route('festivals.update', $festival->id) : route('festivals.store') }}" 
                method="POST"
                class="card p-4"
            >
                @csrf
                @if(isset($festival))
                    @method('PUT')
                @endif

                <!-- Festival Name -->
                <div class="form-group mb-3">
                    <label for="name" class="form-label">Festival Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name', $festival->name ?? '') }}" 
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Festival Status -->
                <div class="form-group mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select 
                        name="status" 
                        id="status" 
                        class="form-control @error('status') is-invalid @enderror"
                    >
                        <option value="1" {{ old('status', $festival->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $festival->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Start Date -->
                <div class="form-group mb-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input 
                        type="date" 
                        name="start_date" 
                        id="start_date" 
                        class="form-control @error('start_date') is-invalid @enderror" 
                        value="{{ old('start_date', $festival->start_date ?? '') }}" 
                        required
                    >
                    @error('start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- End Date -->
                <div class="form-group mb-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input 
                        type="date" 
                        name="end_date" 
                        id="end_date" 
                        class="form-control @error('end_date') is-invalid @enderror" 
                        value="{{ old('end_date', $festival->end_date ?? '') }}" 
                        required
                    >
                    @error('end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Campaign Dropdown -->
                <div class="form-group mb-3">
                    <label for="campaign_id" class="form-label">Campaign</label>
                    <select 
                        name="campaign_id" 
                        id="campaign_id" 
                        class="form-control @error('campaign_id') is-invalid @enderror"
                        required
                    >
                        <option value="">-- Select Campaign --</option>
                        @foreach($campaigns as $id => $name)
                            <option value="{{ $id }}" {{ old('campaign_id', $festival->campaign_id ?? '') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('campaign_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($festival) ? 'Update Festival' : 'Create Festival' }}
                    </button>
                    <a href="{{ route('festivals.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
