@extends('layouts.quizadmin')
@section('content')
<div class="container">
    <h2 class="mb-4">{{ isset($campaign) ? 'Edit Campaign' : 'Create Campaign' }}</h2>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <form 
                action="{{ isset($campaign) ? route('campaigns.update', $campaign->id) : route('campaigns.store') }}" 
                method="POST"
                class="card p-4"
            >
                @csrf
                @if(isset($campaign))
                    @method('PUT')
                @endif

                <!-- Campaign Name -->
                <div class="form-group mb-3">
                    <label for="name" class="form-label">Campaign Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name', $campaign->name ?? '') }}" 
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Campaign Status -->
                <div class="form-group mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select 
                        name="status" 
                        id="status" 
                        class="form-control @error('status') is-invalid @enderror"
                    >
                        <option value="1" {{ old('status', $campaign->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $campaign->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($campaign) ? 'Update Campaign' : 'Create Campaign' }}
                    </button>
                    <a href="{{ route('campaigns.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
