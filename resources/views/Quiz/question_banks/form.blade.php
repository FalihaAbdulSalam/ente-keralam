@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header"> 
            <h4>{{ isset($question) ? 'Edit Question Bank' : 'Create Question Bank' }}</h4>
        </div>
        <div class="card-body">
         
            <form action="{{ isset($question) ? route('admin.question_banks.update', $question->id) : route('admin.question_banks.store') }}" method="POST">
                @csrf
                @if(isset($question)) @method('PUT') @endif
                
                <div class="form-group">
                    <label for="questions">Question</label>
                    <input type="text" name="questions" id="questions" value="{{ $question->questions ?? '' }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="duration">Duration (seconds)</label>
                    <input type="number" name="duration" id="duration" value="{{ $question->duration ?? '' }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="no_of_options">Number of Options</label>
                    <input type="number" name="no_of_options" id="no_of_options" value="{{ $question->no_of_options ?? '' }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="difficulty_level">Difficulty Level</label>
                    <select name="difficulty_level" id="difficulty_level" class="form-control" required>
                        <option value="easy" {{ isset($question) && $question->difficulty_level == 'easy' ? 'selected' : '' }}>Easy</option>
                        <option value="medium" {{ isset($question) && $question->difficulty_level == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="hard" {{ isset($question) && $question->difficulty_level == 'hard' ? 'selected' : '' }}>Hard</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="1" {{ isset($question) && $question->status == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ isset($question) && $question->status == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Options</label>
                    <div id="options-section">
                        @foreach($question->options ?? [null] as $index => $option)
                            <div class="row align-items-center mb-2">
                                <div class="col-md-5">
                                    <input type="text" name="options[{{ $index }}][option_name]" 
                                           class="form-control" placeholder="Option Name" 
                                           value="{{ $option->option_name ?? '' }}" required>
                                </div>
                                <div class="col-md-3">
                                    <select name="options[{{ $index }}][answer_flag]" class="form-control" required>
                                        <option value="1" {{ isset($option) && $option->answer_flag ? 'selected' : '' }}>Correct</option>
                                        <option value="0" {{ isset($option) && !$option->answer_flag ? 'selected' : '' }}>Incorrect</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="options[{{ $index }}][order_no]" 
                                           class="form-control" placeholder="Order" 
                                           value="{{ $option->order_no ?? '' }}" required>
                                </div>
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger btn-sm remove-option">Remove</button>
                                </div>
                            </div>
                        @endforeach
                       
                    </div>
                    <button type="button" id="add-option" class="btn btn-primary btn-sm mt-2">Add Option</button>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-success">{{ isset($question) ? 'Update' : 'Save' }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const addOptionButton = document.getElementById('add-option');
        const optionsSection = document.getElementById('options-section');
        const noOfOptionsInput = document.getElementById('no_of_options');

        // Update the add option button state based on the current number of options
        function updateAddOptionButtonState() {
            const currentOptions = optionsSection.querySelectorAll('.row').length;
            const maxOptions = parseInt(noOfOptionsInput.value, 10) || 0;
            addOptionButton.disabled = currentOptions >= maxOptions;
        }

        // Listen for changes in the number of options
        noOfOptionsInput.addEventListener('input', updateAddOptionButtonState);

        // Add a new option row
        addOptionButton.addEventListener('click', function () {
            const index = optionsSection.querySelectorAll('.row').length;
            const html = `
                <div class="row align-items-center mb-2">
                    <div class="col-md-5">
                        <input type="text" name="options[${index}][option_name]" class="form-control" placeholder="Option Name" required>
                    </div>
                    <div class="col-md-3">
                        <select name="options[${index}][answer_flag]" class="form-control" required>
                            <option value="1">Correct</option>
                            <option value="0">Incorrect</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="options[${index}][order_no]" class="form-control" placeholder="Order" required>
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-danger btn-sm remove-option">Remove</button>
                    </div>
                </div>`;
            optionsSection.insertAdjacentHTML('beforeend', html);
            updateAddOptionButtonState();
        });

        // Remove an option row
        optionsSection.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-option')) {
                e.target.closest('.row').remove();
                updateAddOptionButtonState();
            }
        });

        // Initial check
        updateAddOptionButtonState();
    });
</script>
@endsection
