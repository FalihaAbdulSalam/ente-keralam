@extends('admin.layouts.app')

@section('title', isset($quizQuestion) ? 'Edit Quiz Questions' : 'Add Quiz Questions')

@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">
                {{ isset($quizQuestion) ? 'Edit Quiz Questions' : 'Add Questions to Quiz' }}
            </h4>
            <a href="{{ route('admin.quiz_questions.index') }}" class="btn btn-secondary btn-sm">← Back</a>
        </div>

        <div class="card-body">
            <form 
                action="{{ isset($quizQuestion) ? route('admin.quiz_questions.update', $quizQuestion->id) : route('admin.quiz_questions.store') }}" 
                method="POST">
                @csrf
                @if(isset($quizQuestion))
                    @method('PUT')
                @endif

                {{-- Select Quiz --}}
                <div class="form-group mb-3">
                    <label for="quiz_id" class="form-label fw-semibold">Select Quiz</label>
                    <select name="quiz_id" id="quiz_id" class="form-control" required>
                        <option value="">-- Select Quiz --</option>
                        @foreach($quizzes as $quizItem)
                        <option value="{{ $quizItem->id }}" 
                            {{ old('quiz_id', isset($quiz) ? $quiz->id : '') == $quizItem->id ? 'selected' : '' }}>
                            {{ $quizItem->name }} ({{ $quizItem->topic }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Search and Select Multiple Questions --}}
                <div class="form-group mb-3">
                    <label for="searchBox" class="form-label fw-semibold">Search Question</label>
                    <input type="text" id="searchBox" class="form-control" placeholder="Type to search questions...">
                </div>

                <div class="form-group mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold mb-0">Select Questions</label>
                        <div>
                            <button type="button" id="selectAll" class="btn btn-sm btn-outline-primary me-2">
                                Select All
                            </button>
                            <button type="button" id="deselectAll" class="btn btn-sm btn-outline-secondary">
                                Deselect All
                            </button>
                        </div>
                    </div>

                    <div id="questionContainer" class="border rounded p-3" style="max-height: 420px; overflow-y: auto;">
                        @foreach($questionBanks as $question)
                            <div class="form-check mb-2 question-item">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    name="question_bank_ids[]" 
                                    value="{{ $question->id }}" 
                                    id="question{{ $question->id }}"
                                    {{ in_array($question->id, $alreadyMapped ?? []) ? 'checked' : '' }}>
                                <label class="form-check-label" for="question{{ $question->id }}">
                                    {{ $question->questions }}
                                    <span class="badge bg-secondary ms-2">
                                        {{ ucfirst($question->difficulty_level) }}
                                    </span>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <nav class="mt-3">
                        <ul id="pagination" class="pagination justify-content-center mb-0"></ul>
                    </nav>

                    <small class="text-muted d-block mt-2">
                        Use the search box above to quickly find and select questions.
                    </small>
                </div>

                {{-- Optional Order and Status for single question edit --}}
                @if(isset($quizQuestion))
                    <div class="form-group mb-3">
                        <label for="orderno" class="form-label fw-semibold">Order No</label>
                        <input type="number" name="orderno" id="orderno" class="form-control" 
                               value="{{ old('orderno', $quizQuestion->orderno ?? '') }}" 
                               min="1" placeholder="Enter question order number">
                    </div>

                    <div class="form-group mb-3">
                        <label for="status" class="form-label fw-semibold">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="1" {{ old('status', $quizQuestion->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $quizQuestion->status ?? 0) == 0 ? 'selected' : '' }}>Inactive</option>
                            <option value="2" {{ old('status', $quizQuestion->status ?? 2) == 2 ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                @endif

                {{-- Buttons --}}
                <div class="form-group mt-3 text-end">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($quizQuestion) ? 'Update Quiz Questions' : 'Save Selected Questions' }}
                    </button>
                    <a href="{{ route('admin.quiz_questions.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const itemsPerPage = 10;
    let allQuestions = Array.from(document.querySelectorAll(".question-item"));
    let filteredQuestions = [...allQuestions];
    let currentPage = 1;

    const pagination = document.getElementById("pagination");
    const searchBox = document.getElementById("searchBox");
    const selectAllBtn = document.getElementById("selectAll");
    const deselectAllBtn = document.getElementById("deselectAll");

    // Display items per page
    function displayPage() {
        allQuestions.forEach(q => q.style.display = "none");
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        filteredQuestions.slice(start, end).forEach(q => q.style.display = "block");
    }

    // Render pagination
    function renderPagination() {
        pagination.innerHTML = "";
        const totalPages = Math.ceil(filteredQuestions.length / itemsPerPage);
        if (totalPages <= 1) return;

        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement("li");
            li.classList.add("page-item", i === currentPage ? "active" : "");
            const a = document.createElement("a");
            a.classList.add("page-link");
            a.href = "#";
            a.textContent = i;
            a.addEventListener("click", e => {
                e.preventDefault();
                currentPage = i;
                displayPage();
                renderPagination();
            });
            li.appendChild(a);
            pagination.appendChild(li);
        }
    }

    // Filter questions based on search
    function filterQuestions() {
        const term = searchBox.value.toLowerCase().trim();
        filteredQuestions = allQuestions.filter(q => q.innerText.toLowerCase().includes(term));
        currentPage = 1;
        renderPagination();
        displayPage();
    }

    // Search functionality
    if (searchBox) searchBox.addEventListener("input", filterQuestions);

    // Select / Deselect All
    selectAllBtn?.addEventListener("click", () => {
        filteredQuestions.forEach(q => q.querySelector("input").checked = true);
    });
    deselectAllBtn?.addEventListener("click", () => {
        filteredQuestions.forEach(q => q.querySelector("input").checked = false);
    });

    // Initialize
    renderPagination();
    displayPage();
});
</script>
@endsection
