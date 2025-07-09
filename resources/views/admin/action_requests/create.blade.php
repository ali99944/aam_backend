@extends('layouts.admin')
@section('title', 'إنشاء طلب إجراء يدوي')

@push('styles')
{{-- CodeMirror Base CSS --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/codemirror.min.css" integrity="sha512-uf06llspW44/LZpHzHT6qBOIVODjWtv4MxCricRxkzvopAlSWnTf6hpZTFxuuZcuNE9CBQhqE0Seu1CoRk84nQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
{{-- Optional: Addons CSS (e.g., for linting) --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/6.65.7/addon/lint/lint.min.css" integrity="sha512-SdAiaXRaJEyMMcPcFJGNvYh7XSkl8RJcUktsh9o9JHGbL6UW28HPvyhjpCwGY6Q7Ntx8hHSuF9Qh9EBzQ72aQw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    /* Ensure CodeMirror takes up space and handles validation state */
    .CodeMirror {
        border: 1px solid #ced4da;
        border-radius: .25rem;
        height: auto;
        min-height: 300px; /* Adjust as needed */
        font-size: 0.9em;
        direction: ltr; /* JSON is LTR */
        text-align: left; /* JSON is LTR */
    }
    .CodeMirror-focused {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13,110,253,.25);
        outline: 0;
    }
    /* Style for invalid state */
    .codemirror-invalid > .CodeMirror {
         border-color: var(--danger-color) !important;
    }
    .cm-s-default .cm-error { background-color: #fdd; color: #800; }

    /* RTL adjustments for form */
    html[dir="rtl"] .form-actions .btn:first-child { margin-left: 0.5rem; margin-right: 0;}
    html[dir="rtl"] .me-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }
    html[dir="rtl"] .mr-1 { margin-right: 0 !important; margin-left: 0.25rem !important; }
    html[dir="rtl"] .alert .flex-shrink-0.me-2 { margin-right: 0 !important; margin-left: 0.5rem !important; }

    .alert ul { padding-right: 1.5rem; } /* For RTL lists in alert */
    html[dir="rtl"] .alert ul { padding-left: 0; padding-right: 1.5rem; }
</style>
@endpush

@section('content')
    <div class="content-header">
        <h1>إنشاء طلب إجراء يدوي</h1>
         <a href="{{ route('admin.action-requests.index') }}" class="btn btn-secondary">العودة إلى القائمة</a>
    </div>

    <div class="card">
        <div class="card-header">تفاصيل الطلب</div>
        <div class="card-body">
             <form method="POST" action="{{ route('admin.action-requests.store') }}" class="admin-form">
                @csrf

                {{-- Action Type --}}
                <div class="form-group mb-3">
                    <label for="action_type">نوع الإجراء <span class="text-danger">*</span></label>
                    <select id="action_type" name="action_type" class="form-select @error('action_type') is-invalid @enderror" required>
                        <option value="">-- اختر النوع --</option>
                        @foreach($actionTypes as $key => $label)
                            <option value="{{ $key }}" {{ old('action_type') == $key ? 'selected' : '' }}>
                                {{ $label }} (<code>{{ $key }}</code>)
                            </option>
                        @endforeach
                    </select>
                    @error('action_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                 {{-- Instructions Banner --}}
                <div class="alert alert-info d-flex align-items-start" role="alert">
                     <x-lucide-info class="flex-shrink-0 me-2" style="width:1.5em; height:1.5em;"/>
                    <div>
                        أدخل البيانات اللازمة للإجراء كـ <strong>كائن JSON صالح</strong> في الأسفل. المفاتيح المطلوبة تعتمد على "نوع الإجراء" المحدد.
                        <br>
                        <strong>أمثلة:</strong>
                        <ul>
                            <li><strong>تحديث منتج:</strong> <code>{"product_id": 123, "updates": {"stock": 50, "is_public": true}}</code></li>
                             <li><strong>إلغاء طلب:</strong> <code>{"order_id": 5678, "reason": "طلب العميل"}</code></li>
                             <li><strong>تحقق من مستخدم:</strong> <code>{"user_id": 99}</code></li>
                        </ul>
                     </div>
                </div>

                {{-- Data JSON using CodeMirror --}}
                 <div class="form-group mb-3">
                    <label for="data">حمولة البيانات (JSON) <span class="text-danger">*</span></label>
                     @error('data') <div class="alert alert-danger py-1 px-2">{{ $message }}</div> @enderror
                     {{-- Textarea will be hidden and replaced by CodeMirror --}}
                    <textarea id="data" name="data" class="form-control @error('data') is-invalid @enderror"
                              rows="15" required placeholder='أدخل بيانات JSON، مثال: {"key": "value"}'
                              style="display:none;" {{-- Hide the original textarea --}}
                              >{{ old('data', '{}') }}</textarea>
                    {{-- CodeMirror will be initialized here --}}
                </div>

                {{-- Form Actions --}}
                <div class="form-actions mt-4 pt-3 border-top">
                    <button type="submit" class="btn btn-primary">
                        <x-lucide-plus-circle class="icon-sm ms-1"/> إنشاء الطلب
                    </button>
                    <a href="{{ route('admin.action-requests.index') }}" class="btn btn-secondary">إلغاء</a>
                </div>

            </form>
        </div>
    </div>
@endsection


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
    <script src="https://unpkg.com/jsonlint@1.6.3/web/jsonlint.js"></script>

    <script>
       document.addEventListener('DOMContentLoaded', function () {
           const dataTextArea = document.getElementById("data"); // Renamed variable
           if(dataTextArea) {
               // Ensure textarea is visible before initializing CodeMirror if it was hidden by default for some reason
               // dataTextArea.style.display = 'block'; // Might not be needed if CodeMirror handles it

               const editor = CodeMirror.fromTextArea(dataTextArea, {
                   lineNumbers: true,
                   mode: { name: "javascript", json: true },
                   theme: "default",
                   matchBrackets: true,
                   autoCloseBrackets: true,
                   lineWrapping: true,
                   gutters: ["CodeMirror-lint-markers"],
                   lint: true,
                   // CodeMirror v6 might need `direction: "ltr"` explicitly if it doesn't pick up from element
               });

                // Initially, make sure the textarea is not displayed, CodeMirror will take its place.
                dataTextArea.style.display = 'none';


               const form = dataTextArea.closest('form');
               if(form) {
                   form.addEventListener('submit', function() {
                       editor.save();
                   });
               }

                // Handle validation state styling
                // The original textarea is what receives the is-invalid class from Laravel
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if (mutation.attributeName === "class") {
                            const isInvalid = dataTextArea.classList.contains('is-invalid');
                            // CodeMirror wraps the original textarea, its wrapper is the sibling or specific class
                            editor.getWrapperElement().classList.toggle('codemirror-invalid', isInvalid);
                        }
                    });
                });
                observer.observe(dataTextArea, { attributes: true });
                // Initial check
                editor.getWrapperElement().classList.toggle('codemirror-invalid', dataTextArea.classList.contains('is-invalid'));

           } else {
               console.error("Textarea with ID 'data' not found for CodeMirror initialization.");
           }
       });
    </script>
@endpush