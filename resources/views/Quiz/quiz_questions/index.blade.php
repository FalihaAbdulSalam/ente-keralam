@extends('admin.layouts.app')

@section('title', 'Quiz Questions Summary')

@section('content')
<div class="container">
    <h2 class="mb-4">Quiz Questions Summary</h2>

    {{-- ✅ Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Quizzes</h4>
            <a href="{{ route('admin.quiz_questions.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle me-1"></i>Add Questions to Quiz
            </a>
        </div>

        <div class="card-body">
            <table id="quizTable" class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Quiz Name</th>
                        <th>Topic</th>
                        <th>Total Questions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($quizData as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $row->quiz->name ?? '—' }}</td>
                            <td>{{ $row->quiz->topic ?? '—' }}</td>
                            <td>
                                <span class="badge bg-primary">
                                    {{ $row->total_questions }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.quiz_questions.preview', $row->quiz_id) }}" 
                                   class="btn btn-info btn-sm">
                                   <i class="fas fa-eye"></i>
                                </a>

                                 {{-- Edit Order / Manage --}}
                                 <a href="{{ route('admin.quiz_questions.editByQuiz', $row->quiz_id) }}" 
                                    class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                 </a>
                                 

                                <form action="{{ route('admin.quizzes.destroy', $row->quiz_id) }}" 
                                      method="POST" class="d-inline"
                                      onsubmit="return confirm('Delete this quiz and all related questions?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">
                                No quizzes found with questions.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ✅ DataTables --}}
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Check if DataTable is already initialized
    if (!$.fn.DataTable.isDataTable('#quizTable')) {
        $('#quizTable').DataTable({
            "pageLength": 10,
            "ordering": true,
            "searching": true,
            "info": true,
            "autoWidth": false,
            "responsive": true
        });
    }
});
</script>
@endsection
