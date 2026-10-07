@extends('layouts.quizadmin')

@section('content')
<div class="container">
    <h2>Events</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>List</h4>
            <a href="{{ route('events.create') }}" class="btn btn-success mb-3">Create New Event</a>
        </div>
        <div class="card-body">
        <table id="userTable" class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Festival</th>
                        <th>Eventtype</th>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                        <tr>
                            <td>{{ $event->id }}</td>
                            <td>{{ $event->festival->name }}</td>
                            <td>{{ $event->eventtype->name }}</td>
                            <td>{{ $event->name }}</td>
                            <td>{{ $event->status ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <a href="{{ route('events.edit', $event->id) }}" class="btn btn-primary btn-sm">Edit</a>
                                <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No events found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
