@extends('layouts.quizadmin')

@section('content')
<div class="container">
    <h2 class="mb-4">Question Options</h2>

    <div class="mb-3">
        <a href="{{ route('question_options.create') }}" class="btn btn-success">Create Question Option</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>Question</th>
                <th>Option Name</th>
                <th>Answer Flag</th>
                <th>Order No</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($questionOptions as $option)
                <tr>
                    <td>{{ $option->question->id }}</td>
                    <td>{{ $option->option_name }}</td>
                    <td>{{ $option->answer_flag ? 'Correct' : 'Incorrect' }}</td>
                    <td>{{ $option->order_no }}</td>
                    <td>
                        @if($option->status == 1)
                            Active
                        @elseif($option->status == 0)
                            Inactive
                        @else
                            Cancelled
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('question_options.edit', $option->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('question_options.destroy', $option->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
