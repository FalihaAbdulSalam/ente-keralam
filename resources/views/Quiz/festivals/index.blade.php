@extends('layouts.quizadmin')

@section('content')
<div class="container">
    <h2>Eventtype </h2>
    

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>List</h4>
            <a href="{{ route('festivals.create') }}" class="btn btn-success mb-3">Create New Festival</a>
        </div>
        <div class="card-body">
        <table id="userTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Status</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Campaign</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($festivals as $festival)
                <tr>
                    <td>{{ $festival->id }}</td>
                    <td>{{ $festival->name }}</td>
                    <td>{{ $festival->status ? 'Active' : 'Inactive' }}</td>
                    <td>{{ $festival->start_date }}</td>
                    <td>{{ $festival->end_date }}</td>
                    <td>{{ $festival->campaign->name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('festivals.edit', $festival) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('festivals.destroy', $festival) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
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
