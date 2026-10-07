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

<div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);">
    
    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ✅ Main Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Contest Winners</h5>
        </div>

        <div class="card-body">
        <form action="" method="POST">
        <div class="form-group mb-3">
            <select name="contest_id" id="contest_id" class="form-control" required>
                <option value="">Choose Contest</option>
        @forelse($activeContests as $contest)
        @php
            $contestId = is_array($contest) ? $contest['contest_id'] : $contest->id;
            $contestName = is_array($contest) ? $contest['contest_name'] : $contest->contest_name;
            $title = is_array($contest) ? ($contest['title'] ?? '') : $contest->title;
        @endphp

        <option value="{{ $contestId }}"
            {{ (string)$selectedContestId === (string)$contestId ? 'selected' : '' }}>
            {{ $contestName }}{{ $title ? ' - '.$title : '' }}
        </option>

        @empty
            <option value="" disabled>No active contests available</option>
        @endforelse
            </select>
        </div>
       </form>
            <div class="table-responsive">
                <table id="pollTable" class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email ID</th>
                            <th>Position</th>
                            <th>Additional Point</th>
                            <th>Remarks</th>
                            <th>Updated Date</th>
                        </tr>
                    </thead>
                    <tbody>
                       @forelse($users as $row)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->email }}</td>
                                <td><i class="fas fa-trophy"></i> {{ $row->position }}</td>
                                <td>{{ $row->point }}</td>
                                <td>{{ $row->remarks }}</td>
                                <td>{{ $row->created_at }}</td>
                            </tr>
                        @empty
               
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
  
            </div>
        </div>
    </div>
</div>
<script>
const baseUrl = "{{ url('/') }}/";
document.getElementById('contest_id').addEventListener('change', function () {
    if (this.value) {
        window.location.href = baseUrl + 'admin/contest/winners/' + this.value;
    }
});
</script>
@endsection

{{-- ✅ Scripts --}}
@section('scripts')

<script>
$(document).ready(function() {
    $('#pollTable').DataTable({
        "pageLength": 10,
        "ordering": true,
        "searching": true,
        "info": true,
        "responsive": true,
        "autoWidth": false,
        "language": {
            "search": "🔍 Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ polls",
            "zeroRecords": "No matching polls found"
        }
    });
});
</script>

@endsection
