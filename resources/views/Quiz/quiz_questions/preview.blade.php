@extends('admin.layouts.app')

@section('title', 'Preview Quiz Questions')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Preview Questions - {{ $quiz->name }}</h4>
            
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.quiz_questions.updateOrder') }}">
                @csrf
                <table class="table table-bordered align-middle no-row-click">
                    <thead class="table-light">
                        <tr>
                            <th>Order No</th>
                            <th>Question</th>
                            <th>Difficulty</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($quizQuestions as $qq)
                            <tr class="no-click-event">
                                <td width="120" onclick="event.stopPropagation();">
                                    <input 
                                        type="number" 
                                        name="orderno[{{ $qq->id }}]" 
                                        value="{{ $qq->orderno ?? '' }}" 
                                        class="form-control text-center" 
                                        placeholder="#"
                                        onclick="event.stopPropagation();">
                                </td>
                                <td>{{ $qq->questionBank->questions }}</td>
                                <td>{{ ucfirst($qq->questionBank->difficulty_level) }}</td>
                                <td>
                                    @if($qq->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No questions added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-success">Save Order Numbers</button>
                    <a href="{{ route('admin.quiz_questions.index') }}" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Run this immediately, before DOMContentLoaded
(function() {
    // Override alert to prevent the specific message
    const originalAlert = window.alert;
    window.alert = function(message) {
        // Block alerts that contain "You clicked on" and "orderno"
        if (message && typeof message === 'string' && 
            message.includes('You clicked on') && message.includes('orderno')) {
            console.log('Blocked alert:', message);
            return;
        }
        originalAlert.call(window, message);
    };
})();

document.addEventListener("DOMContentLoaded", function() {
    // Remove all click event listeners from table rows
    const tableRows = document.querySelectorAll('table tbody tr');
    tableRows.forEach(function(row) {
        // Clone and replace to remove all event listeners
        const newRow = row.cloneNode(true);
        row.parentNode.replaceChild(newRow, row);
    });
    
    // Prevent click event from bubbling up when clicking on input fields or their td
    setTimeout(function() {
        document.querySelectorAll('input[type="number"][name^="orderno"]').forEach(function(input) {
            const td = input.closest('td');
            
            // Stop propagation on the td element
            ['click', 'mousedown', 'mouseup'].forEach(function(eventType) {
                td.addEventListener(eventType, function(e) {
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                }, true); // Use capture phase
            });
            
            // Stop propagation on the input element
            ['click', 'mousedown', 'mouseup', 'focus'].forEach(function(eventType) {
                input.addEventListener(eventType, function(e) {
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                }, true); // Use capture phase
            });
        });
    }, 100);
});
</script>
@endsection
