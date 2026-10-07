@extends('admin.layouts.app')

@section('title', isset($sector_detail) ? 'Edit Counter' : 'Create Counter')

@section('content')
<div class="container">

    <h3 class="mb-4">FAQ Management</h3>

    <div class="row">
        <!-- FORM -->
          <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-body p-0">
                    <div class="p-3 bg-primary text-white">Manage FAQ</div>
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">FAQ List</h5>
                            <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary btn-sm">+ Add New Footer</a>
                        </div>
                    </div>
                </div>
            </div>
          </div>

          </div>
        <!-- LIST -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">FAQ List</div>

                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th>English Question</th>
                                <th>Malayalam Question</th>
                                <th width="10%">Order</th>
                                <th width="10%">Status</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                            @forelse($faqs as $faq)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ Str::limit($faq->enquestion,50) }}</td>
                                    <td>{{ Str::limit($faq->malquestion,50) }}</td>
                                    <td>{{ $faq->order }}</td>
                                    <td>
                                        <span class="badge bg-{{ $faq->status ? 'success':'secondary' }}">
                                            {{ $faq->status ? 'Active':'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.faqs.edit',$faq->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                        <form action="{{ route('admin.faqs.destroy',$faq->id) }}" method="POST"
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this FAQ?')">Del</button>
                                        </form>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No FAQs found</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
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
