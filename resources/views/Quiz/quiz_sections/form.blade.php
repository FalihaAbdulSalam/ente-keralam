@extends('layouts.quizadmin')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ isset($quizSection) ? 'Edit Quiz Section' : 'Create Quiz Section' }}</h2>

    <form 
        action="{{ isset($quizSection) ? route('quiz_sections.update', $quizSection->id) : route('quiz_sections.store') }}" 
        method="POST"
        class="card p-4"
    >
        @csrf
        @if(isset($quizSection))
            @method('PUT')
        @endif

        <div class="form-group mb-3">
            <label for="quiz_id" class="form-label">Quiz</label>
            <select name="quiz_id" id="quiz_id" class="form-control">
                @foreach($quizzes as $quiz)
                    <option value="{{ $quiz->id }}" {{ old('quiz_id', $quizSection->quiz_id ?? '') == $quiz->id ? 'selected' : '' }}>
                        {{ $quiz->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="sectiontype_id" class="form-label">Section Type</label>
            <select name="sectiontype_id" id="sectiontype_id" class="form-control">
                <option value="Mock" {{ old('sectiontype_id', $quizSection->sectiontype_id ?? '') == 'Mock' ? 'selected' : '' }}>Mock</option>
                <option value="Live" {{ old('sectiontype_id', $quizSection->sectiontype_id ?? '') == 'Live' ? 'selected' : '' }}>Live</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="quiz_date" class="form-label">Quiz Date</label>
            <input type="date" name="quiz_date" id="quiz_date" class="form-control" value="{{ old('quiz_date', $quizSection->quiz_date ?? '') }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="quiz_time" class="form-label">Quiz Time</label>
            <input type="time" name="quiz_time" id="quiz_time" class="form-control" value="{{ old('quiz_time', $quizSection->quiz_time ?? '') }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="duration" class="form-label">Duration (seconds)</label>
            <input type="number" name="duration" id="duration" class="form-control" value="{{ old('duration', $quizSection->duration ?? '') }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="familarization_time" class="form-label">Familiarization Time (seconds)</label>
            <input type="number" name="familarization_time" id="familarization_time" class="form-control" value="{{ old('familarization_time', $quizSection->familarization_time ?? '') }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="no_of_questions" class="form-label">Number of Questions</label>
            <input type="number" name="no_of_questions" id="no_of_questions" class="form-control" value="{{ old('no_of_questions', $quizSection->no_of_questions ?? '') }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control">
                <option value="1" {{ old('status', $quizSection->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $quizSection->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="form-group mb-3">
            <button type="submit" class="btn btn-primary">
            {{ isset($quizSection) ? 'Update Quiz Section' : 'Create Quiz Section' }}
            </button>
            <a href="{{ route('quiz_sections.index') }}" class="btn btn-secondary">Cancel</a>
        </div>

    </form>
</div>
@endsection
