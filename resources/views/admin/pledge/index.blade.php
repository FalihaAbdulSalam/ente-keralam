@extends('admin.layouts.app')

@section('title', 'Manage Polls')

@push('styles')
    <style>
        .page-header {
            background: white;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: #2c3e50;
        }

        .page-subtitle {
            font-size: 14px;
            color: #666;
            margin: 0;
        }
    </style>
@endpush

@section('content')
    <div class="page-header">
        <h1 class="page-title">Manage Pledge</h1>
        <p class="page-subtitle">Create and manage pledge for users</p>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">
    <div class="d-flex justify-content-between mb-3">
    <h4>Pledge List</h4>
    <a href="{{ route('admin.pledge.create') }}" class="btn btn-success">+ Add New Pledge</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif
    <table class="table table-bordered table-striped align-middle">
    <thead class="table-light">
      <tr>
        <th>#</th>
        <th>Title</th>
        <th>Description</th>
        <th>Start</th>
        <th>End</th>
        <th>Status</th>
        <th width="180">Action</th>
      </tr>
    </thead>
    <tbody>
      @foreach($pledges as $index => $p)
      <tr>
        <td>{{ $index+1 }}</td>
        <td>{{ $p->pledge_title }}</td>
        <td>{{ Str::limit(strip_tags($p->pledge_description), 50) }}</td>
        <td>{{ $p->pledge_startDate }}</td>
        <td>{{ $p->pledge_endDate }}</td>
        <td>
          @if($p->pledge_status)
            <span class="badge bg-success">Active</span>
          @else
            <span class="badge bg-secondary">Inactive</span>
          @endif
        </td>
        <td>
          <a href="{{ route('admin.pledge.edit', $p->pledge_id) }}" class="btn btn-sm btn-primary">Edit</a>
          <a href="{{ route('admin.pledge.toggle', $p->pledge_id) }}" class="btn btn-sm btn-warning">
            {{ $p->pledge_status ? 'Disable' : 'Enable' }}
          </a>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
    </div>
@endsection
