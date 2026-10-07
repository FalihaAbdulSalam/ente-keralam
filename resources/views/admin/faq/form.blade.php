@extends('admin.layouts.app')

@section('title', isset($sector_detail) ? 'Edit Counter' : 'Create Counter')

@section('content')
<div class="container">

    <h3 class="mb-4">FAQ Management</h3>

    <div class="row">
        <!-- FORM -->
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header">
                    {{ isset($editData) ? 'Edit FAQ' : 'Add FAQ' }}
                </div>

                <div class="card-body">
<form method="POST" action="{{ route('admin.faqs.store') }}">
    @csrf

    <!-- ORDER -->
    <div class="mb-3">
        <label class="form-label fw-bold">Order</label>
        <input type="number" name="order" class="form-control" value="1" required>
    </div>

    <!-- STATUS -->
    <div class="mb-3">
        <label class="form-label fw-bold">Status</label>
        <select name="status" class="form-control" required>
            <option value="1" selected>Active</option>
            <option value="0">Inactive</option>
        </select>
    </div>

    <div id="faqRows">

        <!-- FAQ Row -->
        <div class="faq-row border rounded p-3 mb-3">

            <div class="d-flex justify-content-between mb-2">
                <h6 class="fw-bold faq-number">FAQ Item 1</h6>
                <button type="button" class="btn btn-danger btn-sm remove-row d-none">Remove</button>
            </div>

            <!-- ENGLISH -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">English Question</label>
                    <textarea class="form-control" name="faqs[0][enquestion]" required>{{$faq->enquestion ?? 'dd'}}</textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">English Answer</label>
                    <textarea class="form-control" rows="3" name="faqs[0][enanswer]" required>{{$faq->enanswer ?? 'dd'}}</textarea>
                </div>
            </div>

            <!-- MALAYALAM -->
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Malayalam Question</label>
                    <textarea class="form-control" name="faqs[0][malquestion]" required>{{$faq->malquestion ?? ''}}</textarea>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Malayalam Answer</label>
                    <textarea class="form-control" rows="3" name="faqs[0][malanswer]" required>{{$faq->malanswer ?? ''}}</textarea>
                </div>
            </div>

            <input type="hidden" name="faqs[0][user_id]" value="{{ auth()->id() }}" />

        </div>

    </div>

    <button type="button" id="addRow" class="btn btn-secondary mb-3">+ Add FAQ Row</button>
    <button type="submit" class="btn btn-primary w-100">Save FAQs</button>
</form>








                </div>
            </div>
        </div>

      

    </div>

</div>

@endsection

@push('scripts')
    {{-- ✅ Load CKEditor with Upload Plugin --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.2/classic/ckeditor.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let index = 1;

    function updateSerialNumbers() {
        document.querySelectorAll("#faqRows .faq-row").forEach((row, i) => {
            row.querySelector(".faq-number").innerText = "FAQ Item " + (i + 1);
        });
    }

    document.getElementById("addRow").addEventListener("click", function () {
        let container = document.getElementById("faqRows");
        let clone = container.children[0].cloneNode(true);

        index++;

        clone.querySelectorAll("textarea").forEach(t => t.value = "");
        clone.querySelector(".remove-row").classList.remove("d-none");

        // Update name attributes with new index
        clone.querySelectorAll("textarea, input[type=hidden]").forEach(el => {
            let oldName = el.getAttribute("name");
            let newName = oldName.replace(/\[\d+\]/, "[" + (index - 1) + "]");
            el.setAttribute("name", newName);
        });

        container.appendChild(clone);
        updateSerialNumbers();
    });

    // Remove row
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("remove-row")) {
            e.target.closest(".faq-row").remove();
            updateSerialNumbers();
        }
    });
});
</script>


    <script>
        
        document.addEventListener("DOMContentLoaded", function () {
            const editors = document.querySelectorAll('.ckeditor');
            editors.forEach(el => {
                ClassicEditor
                    .create(el, {
                        ckfinder: {
                            uploadUrl: "{{ route('admin.ckeditor.upload').'?_token='.csrf_token() }}"
                        },
                        toolbar: [
                            'heading', '|',
                            'bold', 'italic', 'link', '|',
                            'bulletedList', 'numberedList', '|',
                            'insertTable', 'blockQuote', '|',
                            'imageUpload', 'undo', 'redo'
                        ]
                    })
                    .catch(error => console.error(error));
            });
        });
    </script>
@endpush
