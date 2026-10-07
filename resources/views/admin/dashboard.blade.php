@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back, {{ Auth::guard('admin')->user()->name }}!</p>
    </div>

    <div class="dashboard-cards">
        <div class="card">
            <div class="card-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="card-title">Total News</div>
            <div class="card-value">{{ \App\Models\News::count() ?? 0 }}</div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-check-square"></i>
            </div>
            <div class="card-title">Total Quizzesaaa</div>
            <div class="card-value">{{  0 }}</div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="card-title">Total Tasks</div>
            <div class="card-value">{{ \App\Models\Task::count() ?? 0 }}</div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-title">Total Users</div>
            <div class="card-value">{{ \App\Models\User::count() ?? 0 }}</div>
        </div>

        <div class="card">
            <div class="card-icon">
                <i class="fas fa-poll"></i>
            </div>
            <div class="card-title">Total Polls</div>
            <div class="card-value">{{  0 }}</div>
        </div>
    </div>
@endsection

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
            font-size: 32px;
            font-weight: 700;
            margin: 0 0 10px 0;
            color: #2c3e50;
        }

        .page-subtitle {
            font-size: 14px;
            color: #666;
            margin: 0;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-left: 4px solid;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:nth-child(1) { border-left-color: #667eea; }
        .card:nth-child(2) { border-left-color: #f093fb; }
        .card:nth-child(3) { border-left-color: #4facfe; }
        .card:nth-child(4) { border-left-color: #43e97b; }
        .card:nth-child(5) { border-left-color: #fa709a; }
        .card:nth-child(6) { border-left-color: #feca57; }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .card-icon {
            font-size: 36px;
            margin-bottom: 15px;
        }

        .card-icon i { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card:nth-child(2) .card-icon i { 
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card:nth-child(3) .card-icon i { 
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card:nth-child(4) .card-icon i { 
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card:nth-child(5) .card-icon i { 
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card:nth-child(6) .card-icon i { 
            background: linear-gradient(135deg, #feca57 0%, #ff6348 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .card-title {
            font-size: 13px;
            color: #999;
            margin: 10px 0 5px 0;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-value {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
        }
    </style>
@endpush
