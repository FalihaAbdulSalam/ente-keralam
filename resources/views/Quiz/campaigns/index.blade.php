@extends('layouts.quizadmin')

@section('content')
<div class="container">
    <h2>Campaign </h2>
    

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>List</h4>
            <a href="{{ route('campaigns.create') }}" class="btn btn-success mb-3">Create New Campaign</a>
        </div>
        <div class="card-body">
    <table id="userTable" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($campaigns as $campaign)
                <tr>
                    <td>{{ $campaign->id }}</td>
                    <td>{{ $campaign->name }}</td>
                    <td>{{ $campaign->status ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('campaigns.edit', $campaign->id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form action="{{ route('campaigns.destroy', $campaign->id) }}" method="POST" style="display:inline-block;">
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
