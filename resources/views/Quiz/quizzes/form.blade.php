@extends('admin.layouts.app')

@section('title', isset($quiz) ? 'Edit Quiz' : 'Create Quiz')

@section('content')
<div class="container-fluid mb-4">
    <h2 class="fw-bold mb-4">
        {{ isset($quiz) ? 'Edit Quiz' : 'Create New Quiz' }}
    </h2>

    {{-- ✅ Form Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ isset($quiz) ? 'Update Quiz Details' : 'Add New Quiz' }}</h5>
        </div>

        <div class="card-body">
            <form 
                action="{{ isset($quiz) ? route('admin.quizzes.update', $quiz->id) : route('admin.quizzes.store') }}" 
                method="POST" 
                enctype="multipart/form-data"
            >
                @csrf
                @if(isset($quiz))
                    @method('PUT')
                @endif

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="festival_id" class="form-label fw-semibold">Festival</label>
                        <select name="festival_id" id="festival_id" class="form-control" required>
                           
                            @foreach($festivals as $festival)
                                <option value="{{ $festival->id }}" 
                                    {{ old('festival_id', $quiz->festival_id ?? '') == $festival->id ? 'selected' : '' }}>
                                    {{ $festival->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="event_id" class="form-label fw-semibold">Event</label>
                        <select name="event_id" id="event_id" class="form-control" required>
                            
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" 
                                    {{ old('event_id', $quiz->event_id ?? '') == $event->id ? 'selected' : '' }}>
                                    {{ $event->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Quiz Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="form-control" 
                            value="{{ old('name', $quiz->name ?? '') }}" 
                            placeholder="Enter quiz title" 
                            required>
                    </div>
                    <div class="col-md-6">
                        <label for="section_type" class="form-label fw-semibold">Section Type</label>
                        <select name="section_type" id="section_type" class="form-control" required>
                            <option value="Mock" {{ old('section_type', $quiz->section_type ?? '') == 'Mock' ? 'selected' : '' }}>Mock</option>
                            <option value="Live" {{ old('section_type', $quiz->section_type ?? '') == 'Live' ? 'selected' : '' }}>Live</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="topic" class="form-label fw-semibold">Topic</label>
                        <input 
                            type="text" 
                            name="topic" 
                            id="topic" 
                            class="form-control" 
                            placeholder="Eg. Energy, Environment, General Knowledge" 
                            value="{{ old('topic', $quiz->topic ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label fw-semibold">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" 
                               value="{{ old('start_date', $quiz->start_date ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label fw-semibold">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" 
                               value="{{ old('end_date', $quiz->end_date ?? '') }}">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="start_time" class="form-label fw-semibold">Start Time</label>
                        <input type="time" name="start_time" id="start_time" class="form-control" 
                               value="{{ old('start_time', $quiz->start_time ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_time" class="form-label fw-semibold">End Time</label>
                        <input type="time" name="end_time" id="end_time" class="form-control" 
                               value="{{ old('end_time', $quiz->end_time ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="poster" class="form-label fw-semibold">Poster</label>
                        <input type="file" name="poster" id="poster" class="form-control">
                        @if(isset($quiz) && $quiz->poster)
                            <small class="text-muted">Current: {{ $quiz->poster }}</small>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label for="banner" class="form-label fw-semibold">Banner</label>
                        <input type="file" name="banner" id="banner" class="form-control">
                        @if(isset($quiz) && $quiz->banner)
                            <small class="text-muted">Current: {{ $quiz->banner }}</small>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">

                <div class="col-md-6">
                    <label for="about" class="form-label fw-semibold">About</label>
                    <textarea name="about" id="about" rows="3" class="form-control" placeholder="Brief description of the quiz">{{ old('about', $quiz->about ?? '') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label for="terms_condition" class="form-label fw-semibold">Terms & Conditions</label>
                    <textarea name="terms_condition" id="terms_condition" rows="3" class="form-control" placeholder="Enter quiz terms and conditions">{{ old('terms_condition', $quiz->terms_condition ?? '') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label for="result" class="form-label fw-semibold">Result Info</label>
                    <textarea name="result" id="result" rows="2" class="form-control" placeholder="When and where the results will be announced">{{ old('result', $quiz->result ?? '') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label for="attachment" class="form-label fw-semibold">Attachment (Optional)</label>
                    <input type="file" name="attachment" id="attachment" class="form-control">
                    @if(isset($quiz) && $quiz->attachment)
                        <small class="text-muted">Current: {{ $quiz->attachment }}</small>
                    @endif
                </div>

                <div class="col-md-6">
                    <label for="points" class="form-label fw-semibold">Points</label>
                    <input 
                        type="number" 
                        name="points" 
                        id="points" 
                        class="form-control" 
                        placeholder="Enter points for this quiz"
                        min="0"
                        value="{{ old('points', $quiz->points ?? 0) }}">
                    <small class="text-muted">This defines how many points the quiz is worth.</small>
                </div>

                <div class="col-md-6">
                    <label for="status" class="form-label fw-semibold">Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="1" {{ old('status', $quiz->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $quiz->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        
                        {{ isset($quiz) ? 'Update Quiz' : 'Create Quiz' }}
                    </button>
                    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary px-4">
                         Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
