@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>Question Banks</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>List</h4>
            <a href="{{ route('admin.question_banks.create') }}" class="btn btn-sm bg-primary text-white">
                <i class="fas fa-plus-circle me-1"></i> Add Question 
            </a>
        </div>

        <div class="card-body">
            <table id="userTable" class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Question</th>
                        <th>Duration (seconds)</th>
                        <th>Options</th>
                        <th>Difficulty Level</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($questions as $question)
                        <tr>
                            <td>{{ $question->id }}</td>
                            <td>{{ $question->questions }}</td>
                            <td>{{ $question->duration }}</td>
                            <td>
                                @if($question->options->isNotEmpty())
                                    <ul class="option-list">
                                        @foreach($question->options as $option)
                                            <li @if($option->answer_flag==1) class="fw-bold text-success" @endif>
                                                {{ $option->option_name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-muted">No options available</span>
                                @endif
                            </td>
                            <td>{{ ucfirst($question->difficulty_level) }}</td>
                            <td>
                                <span class="{{ $question->status === '1' ? 'text-success' : 'text-danger' }}">
                                    {{ $question->status === '1' ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.question_banks.edit', $question->id) }}" 
                                   class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.question_banks.destroy', $question->id) }}" 
                                      method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-outline-danger btn-sm" 
                                            onclick="return confirm('Are you sure?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Inline CSS for clarity --}}
<style>
    .option-list {
        list-style-type: disc;
        padding-left: 20px;
        margin: 0;
    }
    .option-list li {
        list-style: disc;
        margin-bottom: 4px;
    }
    .option-list li.fw-bold {
        font-weight: bold;
    }
    .option-list li.text-success::marker {
        color: green;
    }
</style>
@endsection
