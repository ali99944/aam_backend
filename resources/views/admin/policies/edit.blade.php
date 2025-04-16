@extends('layouts.admin')
@section('title', 'Edit Policy - AAM Store')

@push('styles')
{{-- Add any specific styles if needed --}}
@endpush

@section('content')
    <div class="content-header">
        <h1>Edit Policy: <span class="text-primary">{{ $policy->name }}</span></h1>
         <span class="badge bg-light text-dark border">Key: {{ $policy->key }}</span>
    </div>

    <form method="POST" action="{{ route('admin.policies.update', $policy->id) }}" class="admin-form" id="policy-form">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-body">
                 {{-- Name --}}
                <div class="form-group mb-3">
                    <label for="name">Policy Name <span class="text-danger">*</span></label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $policy->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 {{-- Key (Readonly recommended after creation) --}}
                <div class="form-group mb-3">
                    <label for="key">Policy Key <span class="text-danger">*</span></label>
                    <input type="text" id="key" name="key" class="form-control @error('key') is-invalid @enderror"
                           value="{{ old('key', $policy->key) }}" required readonly {{-- Make readonly after creation --}}>
                    <small class="form-text text-muted">Unique identifier (e.g., 'privacy-policy'). Cannot be changed easily.</small>
                    @error('key') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Content (WYSIWYG) --}}
                <div class="form-group mb-3">
                    <label for="content">Content <span class="text-danger">*</span></label>
                    {{-- Add a specific ID for TinyMCE to target --}}
                    <textarea id="policy-content-editor" name="content" class="form-control wysiwyg-editor @error('content') is-invalid @enderror"
                              rows="20">{{ old('content', $policy->content) }}</textarea>
                     @error('content') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror {{-- Ensure block display for textarea errors --}}
                </div>

                 <div class="form-actions mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">
                        <x-lucide-save class="icon-sm mr-1"/> Update Policy
                    </button>
                    <a href="{{ route('admin.policies.index') }}" class="btn btn-secondary">Cancel</a>
                     {{-- Link to Preview Page --}}
                    <a href="{{ route('admin.policies.show', $policy->id) }}" class="btn btn-outline-info float-end" target="_blank">
                        <x-lucide-eye class="icon-sm mr-1"/> Preview Changes
                    </a>
                 </div>

            </div> {{-- End Card Body --}}
        </div> {{-- End Card --}}
    </form>
@endsection


@push('scripts')
{{-- Load TinyMCE from CDN --}}
<script src="https://cdn.tiny.cloud/1/5zw3ok7a382r6ge5omb9ep6uyr5ue2khuh6palx5ma9o856z/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> {{-- Replace with your API key from tiny.cloud --}}

<script>
  document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
      selector: 'textarea#policy-content-editor', // Target the specific textarea
      plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
      menubar: 'file edit view insert format tools table help',
      toolbar: 'undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image media | table | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl',
      autosave_ask_before_unload: true,
      autosave_interval: '30s',
      autosave_prefix: '{path}{query}-{id}-',
      autosave_restore_when_empty: false,
      autosave_retention: '2m',
      height: 600,
      image_caption: true,
      quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
      noneditable_class: 'mceNonEditable',
      toolbar_mode: 'sliding',
      contextmenu: 'link image table',
      // Add image upload configuration if needed
      // images_upload_url: '{{-- route('admin.tinymce.upload') --}}', // Example route for handling uploads
      // images_upload_handler: function (blobInfo, success, failure) { ... }, // Custom handler
      content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
      // Handle dark mode if your admin panel supports it
      // skin: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide'),
      // content_css: (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default')
       setup: function (editor) {
            // Optional: Persist content to textarea on change for potential live preview JS
            // editor.on('change', function () {
            //     editor.save(); // Updates the underlying textarea value
            //     // Add JS here to update a preview iframe/div if implementing live preview
            // });
        }
    });
  });
</script>
@endpush

{{-- Add badge styles if not global --}}
@push('styles')
<style>.badge.bg-light { background-color: #f8f9fa!important; } .text-dark { color: #212529!important; } .border { border: 1px solid #dee2e6!important; } .float-end { float: right !important; }</style>
@endpush