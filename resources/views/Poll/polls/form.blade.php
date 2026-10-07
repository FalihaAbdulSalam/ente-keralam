@extends('admin.layouts.app')

@section('title', isset($poll) ? 'Edit Poll' : 'Create Poll')

@section('content')
<div class="container-fluid mb-4">
    <h2 class="fw-bold mb-4">
        {{ isset($poll) ? 'Edit Poll' : 'Create New Poll' }}
    </h2>

    {{-- ✅ Form Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ isset($poll) ? 'Update Poll Details' : 'Add New Poll' }}</h5>
        </div>

        <div class="card-body">
            <form 
                action="{{ isset($poll) ? route('admin.polls.update', $poll->id) : route('admin.polls.store') }}" 
                method="POST" 
                enctype="multipart/form-data"
            >
                @csrf
                @if(isset($poll))
                    @method('PUT')
                @endif

                {{-- Row 1: Festival & Event --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="festival_id" class="form-label fw-semibold">Festival</label>
                        <select name="festival_id" id="festival_id" class="form-control" required>
                           
                            @foreach($festivals as $festival)
                                <option value="{{ $festival->id }}" 
                                    {{ old('festival_id', $poll->festival_id ?? '') == $festival->id ? 'selected' : '' }}>
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
                                    {{ old('event_id', $poll->event_id ?? '') == $event->id ? 'selected' : '' }}>
                                    {{ $event->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Row 2: Poll Name & Topic --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Poll Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            class="form-control" 
                            value="{{ old('name', $poll->name ?? '') }}" 
                            placeholder="Enter poll title" 
                            required>
                    </div>
                    <div class="col-md-6">
                        <label for="topic" class="form-label fw-semibold">Topic</label>
                        <input 
                            type="text" 
                            name="topic" 
                            id="topic" 
                            class="form-control" 
                            placeholder="Eg. Climate, Development, General Awareness" 
                            value="{{ old('topic', $poll->topic ?? '') }}">
                    </div>
                </div>

                {{-- Row 3: Dates --}}
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label fw-semibold">Start Date</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" 
                               value="{{ old('start_date', $poll->start_date ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label fw-semibold">End Date</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" 
                               value="{{ old('end_date', $poll->end_date ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="start_time" class="form-label fw-semibold">Start Time</label>
                        <input type="time" name="start_time" id="start_time" class="form-control" 
                               value="{{ old('start_time', $poll->start_time ?? '') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_time" class="form-label fw-semibold">End Time</label>
                        <input type="time" name="end_time" id="end_time" class="form-control" 
                               value="{{ old('end_time', $poll->end_time ?? '') }}">
                    </div>
                </div>

                {{-- Row 4: Files --}}
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="poster" class="form-label fw-semibold">Poster</label>
                        <input type="file" name="poster" id="poster" class="form-control">
                        @if(isset($poll) && $poll->poster)
                            <small class="text-muted d-block mt-1">Current: {{ $poll->poster }}</small>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label for="banner" class="form-label fw-semibold">Banner</label>
                        <input type="file" name="banner" id="banner" class="form-control">
                        @if(isset($poll) && $poll->banner)
                            <small class="text-muted d-block mt-1">Current: {{ $poll->banner }}</small>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label for="attachment" class="form-label fw-semibold">Attachment (Optional)</label>
                        <input type="file" name="attachment" id="attachment" class="form-control">
                        @if(isset($poll) && $poll->attachment)
                            <small class="text-muted d-block mt-1">Current: {{ $poll->attachment }}</small>
                        @endif
                    </div>
                </div>

                {{-- Row 5: About & Terms --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="about" class="form-label fw-semibold">About</label>
                        <textarea name="about" id="about" rows="3" class="form-control" placeholder="Brief description of the poll">{{ old('about', $poll->about ?? '') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="terms_condition" class="form-label fw-semibold">Terms & Conditions</label>
                        <textarea name="terms_condition" id="terms_condition" rows="3" class="form-control" placeholder="Enter poll terms and conditions">{{ old('terms_condition', $poll->terms_condition ?? '') }}</textarea>
                    </div>
                </div>

                {{-- Row 6: Points & Status --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="points" class="form-label fw-semibold">Points</label>
                        <input 
                            type="number" 
                            name="points" 
                            id="points" 
                            class="form-control" 
                            placeholder="Enter points for this poll"
                            min="0"
                            value="{{ old('points', $poll->points ?? 0) }}">
                        <small class="text-muted">Defines how many points this poll carries.</small>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="1" {{ old('status', $poll->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $poll->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                </div>

                {{-- ✅ Poll Questions Section - Now in Table Format --}}
                <div class="mb-4">
                    <label class="form-label fw-semibold"><strong>Poll Questions & Options</strong></label>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="pollQuestionTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="35%">Question</th>
                                    <th>Options</th>
                                    <th width="10%">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="questions-wrapper">
                                @if(isset($poll) && $poll->questions)
                                    @foreach($poll->questions as $index => $question)
                                        <tr>
                                            <td>
                                                <input type="text" name="questions[{{ $index }}][text]" class="form-control" value="{{ $question->question_text }}" placeholder="Enter question">
                                            </td>
                                            <td>
                                                <div class="options-wrapper">
                                                    @foreach($question->options as $option)
                                                        <input type="text" name="questions[{{ $index }}][options][]" class="form-control mb-2" value="{{ $option->option_text }}" placeholder="Enter option">
                                                    @endforeach
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-secondary add-option">+ Add Option</button>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm remove-question"><i class="fas fa-trash-alt"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td>
                                            <input type="text" name="questions[0][text]" class="form-control" placeholder="Enter question">
                                        </td>
                                        <td>
                                            <div class="options-wrapper">
                                                <input type="text" name="questions[0][options][]" class="form-control mb-2" placeholder="Option 1">
                                                <input type="text" name="questions[0][options][]" class="form-control mb-2" placeholder="Option 2">
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary add-option">+ Add Option</button>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-question"><i class="fas fa-trash-alt"></i></button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-question">+ Add Question</button>
                </div>

                {{-- Submit Buttons --}}
                <div class="text-end">
                    <button type="submit" class="btn btn-success px-4">
                        {{ isset($poll) ? 'Update Poll' : 'Create Poll' }}
                    </button>
                    <a href="{{ route('admin.polls.index') }}" class="btn btn-secondary px-4">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ✅ Dynamic JS for adding/removing questions/options --}}
<script>
let questionCount = {{ isset($poll) && $poll->questions ? count($poll->questions) : 1 }};

document.getElementById('add-question').addEventListener('click', function() {
    const wrapper = document.getElementById('questions-wrapper');
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>
            <input type="text" name="questions[${questionCount}][text]" class="form-control" placeholder="Enter question">
        </td>
        <td>
            <div class="options-wrapper">
                <input type="text" name="questions[${questionCount}][options][]" class="form-control mb-2" placeholder="Option 1">
                <input type="text" name="questions[${questionCount}][options][]" class="form-control mb-2" placeholder="Option 2">
            </div>
            <button type="button" class="btn btn-sm btn-outline-secondary add-option">+ Add Option</button>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-outline-danger btn-sm remove-question"><i class="fas fa-trash-alt"></i></button>
        </td>
    `;
    wrapper.appendChild(newRow);
    questionCount++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('add-option')) {
        const parent = e.target.closest('td').querySelector('.options-wrapper');
        const input = document.createElement('input');
        input.type = 'text';
        input.name = e.target.closest('td').querySelectorAll('.options-wrapper input')[0].name;
        input.classList = 'form-control mb-2';
        input.placeholder = 'New Option';
        parent.appendChild(input);
    }

    if (e.target.classList.contains('remove-question') || e.target.closest('.remove-question')) {
        e.target.closest('tr').remove();
    }
});
</script>
@endsection
