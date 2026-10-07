@extends('layouts.quizadmin')

@section('content')
<div class="container">
    <h2 class="mb-4">{{ isset($questionOption) ? 'Edit Question Option' : 'Create Question Option' }}</h2>

    <form action="{{ isset($questionOption) ? route('question_options.update', $questionOption->id) : route('question_options.store') }}" method="POST">
        @csrf
        @if(isset($questionOption))
            @method('PUT')
        @endif

        <div class="form-group mb-3">
            <label for="question_id" class="form-label">Question</label>
            <select name="question_id" id="question_id" class="form-control" required>
                @foreach($questions as $question)
                    <option value="{{ $question->id }}" {{ old('question_id', $questionOption->question_id ?? '') == $question->id ? 'selected' : '' }}>
                        {{ $question->id }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="option_name" class="form-label">Option Name</label>
            <input type="text" name="option_name" id="option_name" class="form-control" value="{{ old('option_name', $questionOption->option_name ?? '') }}" required>
        </div>

        <div class="form-group mb-3">
            <label for="answer_flag" class="form-label">Answer Flag</label>
            <select name="answer_flag" id="answer_flag" class="form-control" required>
                <option value="1" {{ old('answer_flag', $questionOption->answer_flag ?? '') == 1 ? 'selected' : '' }}>Correct</option>
                <option value="0" {{ old('answer_flag', $questionOption->answer_flag ?? '') == 0 ? 'selected' : '' }}>Incorrect</option>
            </select>
        </div>

        <div class="form-group mb-3">
            <label for="order_no" class="form-label">Order No</label>
            <input type="number" name="order_no" id="order_no" class="form-control" value="{{ old('order_no', $questionOption->order_no ?? '') }}">
        </div>

        <div class="form-group mb-3">
            <label for="status" class="form-label">Status</label>
            <select name="status" id="status" class="form-control" required>
                <option value="1" {{ old('status', $questionOption->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('status', $questionOption->status ?? 0) == 0 ? 'selected' : '' }}>Inactive</option>
                <option value="2" {{ old('status', $questionOption->status ?? 2) == 2 ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            {{ isset($questionOption) ? 'Update Question Option' : 'Create Question Option' }}
        </button>
    </form>
</div>
@endsection
