@extends('layouts.admin')
@section('title', 'Add New Policy - AAM Store')

{{-- Add TinyMCE scripts needed for the form --}}
@push('scripts')
{{-- Load TinyMCE from CDN --}}
<script src="https://cdn.tiny.cloud/1/5zw3ok7a382r6ge5omb9ep6uyr5ue2khuh6palx5ma9o856z/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script> {{-- Replace with your API key --}}

<script>
  document.addEventListener('DOMContentLoaded', function() {
    tinymce.init({
      selector: 'textarea#policy-content-editor', // Target the specific textarea
      plugins: 'preview importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
      menubar: 'file edit view insert format tools table help',
      toolbar: 'undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image media | table | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | save print | pagebreak anchor codesample | ltr rtl',
      height: 600,
      // Add other common TinyMCE options from your edit view's script
       content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
    });
  });
</script>
@endpush

@section('content')
    <div class="content-header">
        <h1>Add New Policy</h1>
    </div>

    <form method="POST" action="{{ route('admin.policies.store') }}" class="admin-form" id="policy-form">
        {{-- Pass an empty policy object or null to the form partial --}}
        {{-- The form partial needs to handle the case where $policy might not exist --}}
        {{-- We need to adjust the _form slightly or pass a new Policy() --}}
        @include('admin.policies._form', ['policy' => $policy])
    </form>
@endsection