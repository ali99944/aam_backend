{{-- resources/views/admin/store_page_sections/_form.blade.php --}}
@csrf

{{-- Basic Section Info --}}
<div class="row">
    <div class="col-md-6 form-group mb-3">
        <label for="name">Section Name <span class="text-danger">*</span></label>
        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $section->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
     <div class="col-md-6 form-group mb-3">
        <label for="key">Section Key <span class="text-danger">*</span></label>
        <input type="text" id="key" name="key" class="form-control @error('key') is-invalid @enderror"
               value="{{ old('key', $section->key ?? '') }}" required pattern="[a-z0-9_-]+" title="Lowercase letters, numbers, underscore, hyphen only">
         <small class="form-text text-muted">Unique identifier (lowercase, numbers, _, -).</small>
        @error('key') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>
</div>

<hr>

{{-- JSON Content Textarea with CodeMirror --}}
<h4 class="mb-2">Section Content</h4>

{{-- Instructions Banner --}}
<div class="alert alert-info d-flex align-items-start" role="alert">
    <x-lucide-info class="flex-shrink-0 me-2" style="width:1.5em; height:1.5em;"/>
    <div>
        Enter the section content as a <strong>valid JSON object</strong> (key-value pairs).
        <br>
        Keys should be strings, and values can be strings, numbers, booleans (<code>true</code>/<code>false</code>), arrays (<code>[]</code>), or nested objects (<code>{}</code>).
        <br>
    </div>
</div>

{{-- Display JSON Validation Errors --}}
@error('content')
    <div class="alert alert-danger">
        {{ $message }}
    </div>
@enderror


{{-- Content Textarea (Target for CodeMirror) --}}
<div class="form-group mb-3">
    <label for="content">Content JSON <span class="text-danger">*</span></label>
    <textarea id="content" name="content" class="form-control @error('content') is-invalid @enderror"
              rows="15" required placeholder="Enter valid JSON object here..."
              >{{ old('content', isset($section->content) ? json_encode($section->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '{}') }}</textarea>
    {{-- CodeMirror will replace this, but keep for fallback/no-JS --}}
</div>


{{-- Form Actions --}}
<div class="form-actions mt-4 pt-3 border-top">
    <button type="submit" class="btn btn-primary">
        <x-lucide-save class="icon-sm mr-1"/> {{ isset($section) ? 'Update Section' : 'Create Section' }}
    </button>
    <a href="{{ route('admin.store-pages.show', $storePage->id) }}" class="btn btn-secondary">Cancel</a>
</div>

{{-- CodeMirror Integration --}}
@push('styles')
    {{-- CodeMirror Base CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css" integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- Optional: Addons CSS (e.g., for linting) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/lint/lint.min.css" integrity="sha512-SdAiaXRaJEyMMcPcFJGNvYh7XSkl8RJcUktsh9o9JHGbL6UW28HPvyhjpCwGY6Q7Ntx8hHSuF9Qh9EBzQ72aQw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- Optional: Theme CSS (e.g., Material) --}}
    {{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/theme/material.min.css"> --}}
    <style>
        /* Ensure CodeMirror takes up space and handles validation state */
        .CodeMirror {
            border: 1px solid #ced4da;
            border-radius: .25rem;
            height: auto;
            min-height: 300px; /* Adjust as needed */
            font-size: 0.9em;
        }
        .CodeMirror-focused {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
            outline: 0;
        }
        /* Style for invalid state */
        .form-control.is-invalid + .CodeMirror {
             border-color: var(--danger-color) !important;
        }
        .cm-s-default .cm-error { background-color: #fdd; color: #800; } /* Default theme error style */
    </style>
@endpush

@push('scripts')
    {{-- CodeMirror Core JS --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.js" integrity="sha512-8RnEqURPUc5aqFEN04aQEiPlSAdE0jlFS/9iGgUyNtwFnSKCXhmB6ZTNl7LnDtDWKabJIASzXrzD0K+LYexU9g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- JSON Mode --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/mode/javascript/javascript.min.js" integrity="sha512-I6CdJdruzGtvDyvdO4YsiAq+pkWf2efgd1ZUSK2FnM/u2VuRASPC7GowWQrWyjK6h8WuG+YycLAo7sJ1G26PqA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- Addons (Optional but Recommended) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/edit/matchbrackets.min.js" integrity="sha512-kfAu9PV9HxcF5u6s/OpWRFB5W6HSE6GX1FFdkMXeQjIIT49r9FdtS9R6vj+54ZJTE2r6Xw1k15aAL+o+xQ8w5Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/edit/closebrackets.min.js" integrity="sha512-tsjcYOOKhsDQNryh65Hr4GgIv/X2kwsFmGOzWUoj0kPZxB0Glcbk8S1L3DT/fDQK047m2vU/y444wT9yYFfcQg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/lint/lint.min.js" integrity="sha512-PLkQaKfIKUT6gCkh9H99ZjcjWGBX1bKGEf3zZ5vC/6dT7TCnXmLZ9xSj6Uo6Kx69Tx1VjL+aRzJMH80JvjFqtg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/lint/json-lint.min.js" integrity="sha512-mgNQKMz5M9PxS4VSEDNWJkd3RFHqDt/0VI5nk8QvqfH4mRz7gMzzrDq8uKQGDjCH63GcU20QVzrfu22qrJ3X+A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    {{-- JSON Lint requires a JSON parser (window.jsonlint = ...) --}}
    <script src="https://unpkg.com/jsonlint@1.6.3/web/jsonlint.js"></script> {{-- Or use a different linter --}}

    <script>
       document.addEventListener('DOMContentLoaded', function () {
           const contentTextArea = document.getElementById("content");
           if(contentTextArea) {
               const editor = CodeMirror.fromTextArea(contentTextArea, {
                   lineNumbers: true,
                   mode: { name: "javascript", json: true }, // Correct mode for JSON
                   theme: "default", // Or choose another theme like 'material'
                   matchBrackets: true,
                   autoCloseBrackets: true,
                   lineWrapping: true,
                   gutters: ["CodeMirror-lint-markers"],
                   lint: true // Enable linting
               });

               // Refresh editor if inside a modal or initially hidden element
               // Example: if using Bootstrap modal:
               // const modalElement = document.getElementById('yourModalId');
               // if (modalElement) {
               //    modalElement.addEventListener('shown.bs.modal', function () {
               //        editor.refresh();
               //    });
               // }

               // Ensure textarea is updated on form submit (CodeMirror v5/6 saves automatically on form submit by default, but explicit save is safer)
               const form = contentTextArea.closest('form');
               if(form) {
                   form.addEventListener('submit', function() {
                       editor.save(); // Updates the original textarea value before submitting
                   });
               }

                // Handle validation state styling
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === "class") {
                            const isInvalid = contentTextArea.classList.contains('is-invalid');
                            editor.getWrapperElement().classList.toggle('codemirror-invalid', isInvalid);
                        }
                    });
                });
                observer.observe(contentTextArea, { attributes: true });
                // Initial check
                editor.getWrapperElement().classList.toggle('codemirror-invalid', contentTextArea.classList.contains('is-invalid'));


           } else {
               console.error("Textarea with ID 'content' not found for CodeMirror initialization.");
           }
       });
    </script>
    {{-- Add style for invalid state --}}
    <style>.codemirror-invalid > .CodeMirror { border-color: var(--danger-color) !important; }</style>

@endpush