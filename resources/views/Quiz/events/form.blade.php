@extends('layouts.quizadmin')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ isset($event) ? 'Edit Event' : 'Create Event' }}</h2>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <form 
                action="{{ isset($event) ? route('events.update', $event->id) : route('events.store') }}" 
                method="POST"
                class="card p-4"
            >
                @csrf
                @if(isset($event))
                    @method('PUT')
                @endif

                <!-- Festival Dropdown -->
                <div class="form-group mb-3">
                    <label for="festival_id" class="form-label">Festival</label>
                    <select 
                        name="festival_id" 
                        id="festival_id" 
                        class="form-control @error('festival_id') is-invalid @enderror"
                        required
                    >
                        <option value="">-- Select Festival --</option>
                        @foreach($festivals as $id => $name)
                            <option value="{{ $id }}" {{ old('festival_id', $event->festival_id ?? '') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('festival_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Eventtype Dropdown -->
                <div class="form-group mb-3">
                    <label for="eventtype_id" class="form-label">Eventtype</label>
                    <select 
                        name="eventtype_id" 
                        id="eventtype_id" 
                        class="form-control @error('eventtype_id') is-invalid @enderror"
                        required
                    >
                        <option value="">-- Select Eventtype --</option>
                        @foreach($eventtypes as $id => $name)
                            <option value="{{ $id }}" {{ old('eventtype_id', $event->eventtype_id ?? '') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('eventtype_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Event Name -->
                <div class="form-group mb-3">
                    <label for="name" class="form-label">Event Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        class="form-control @error('name') is-invalid @enderror" 
                        value="{{ old('name', $event->name ?? '') }}" 
                        required
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Event Status -->
                <div class="form-group mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select 
                        name="status" 
                        id="status" 
                        class="form-control @error('status') is-invalid @enderror"
                    >
                        <option value="1" {{ old('status', $event->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $event->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($event) ? 'Update Event' : 'Create Event' }}
                    </button>
                    <a href="{{ route('events.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
