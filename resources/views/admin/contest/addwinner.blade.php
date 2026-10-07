@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">Add Winner</h2>
    {{-- ✅ Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <form action="{{ route('admin.contest.store-winner') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label for="contest_id" class="form-label">Contest Title</label>
            <select name="contest_id" id="contest_id" class="form-control"  data-live-search="true" required>
                <option value="">-- Select Contest --</option>
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
        <div class="form-group mb-3">
            <label for="user_id" class="form-label">Participent</label>
            <select name="user_id" id="user_id" class="form-control" required>
                <option value="">-- Select a User --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
            </select>
        </div>

        
        <div class="form-group mb-3">
            <label for="status" class="form-label">Result</label>
            <select name="position" id="position" class="form-control" required>
                <option value="" >Select Prize</option>
                <option value="1">1St Prize</option>
                <option value="2">2nd Prize</option>
                <option value="3">3rd Prize</option>
                
            </select>
        </div>
        <div class="form-group mb-3">
            <label for="grade" class="form-label">Point</label>
            <input type="number" name="point" id="point" class="form-control" value="" required>
        </div>
        <div class="form-group mb-3">
            <label for="grade" class="form-label">Remarks</label>
            <textarea name="remarks" id="remarks" class="form-control" required></textarea>
        </div>



        <button type="submit" class="btn btn-primary">
            Add Winner
        </button>
    </form>
</div>
<script>
const baseUrl = "{{ url('/') }}/";
document.getElementById('contest_id').addEventListener('change', function () {
    if (this.value) {
        window.location.href = baseUrl + 'admin/contest/addwinner/' + this.value;
    }
});
</script>
@endsection
