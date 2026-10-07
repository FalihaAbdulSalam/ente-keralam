@extends('layouts.quizadmin')

@section('content')
<div class="container">
    <h2>Quiz Sections</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>List</h4>
            <a href="{{ route('quiz_sections.create') }}" class="btn btn-success mb-3">Create New Quiz Section</a>
        </div>
        <div class="card-body">
        <table id="userTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Quiz</th>
                        <th>Section Type</th>
                        <th>Quiz Date</th>
                        <th>Quiz Time</th>
                        <th>Duration</th>
                        <th>Questions</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quizSections as $section)
                        <tr>
                            <td>{{ $section->id }}</td>
                            <td>{{ $section->quiz->name }}</td>
                            <td>{{ $section->sectiontype_id }}</td>
                            <td>{{ $section->quiz_date }}</td>
                            <td>{{ $section->quiz_time }}</td>
                            <td>{{ $section->duration }}s</td>
                            <td>{{ $section->no_of_questions }}</td>
                            <td>{{ $section->status ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <a href="{{ route('quiz_sections.edit', $section->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('quiz_sections.destroy', $section->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                   
                    @endforeach
                </tbody>
            </table>

           
        </div>
    </div>
</div>
@endsection
