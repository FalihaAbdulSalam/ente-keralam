@extends('admin.layouts.app')

@section('title', 'Manage Tasks')

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
        <h1 class="page-title">Manage Tasks</h1>
        <p class="page-subtitle">Create and manage tasks for users</p>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">
        <p style="color: #999; text-align: center;">Tasks management interface coming soon...</p>
    </div>
@endsection
