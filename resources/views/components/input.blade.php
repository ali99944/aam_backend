@props([
    'type' => 'text',
    'name',
    'id' => null,
    'label' => null,
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'icon' => null, // Optional icon class
    'groupClass' => 'form-group' // Allow overriding wrapper class
])

@php
    // Auto-generate id from name if not provided
    $id = $id ?? $name;
    $value = old($name, $value); // Use old input helper for repopulation
@endphp

<div class="{{ $groupClass }}">
    @if($label)
        <label for="{{ $id }}">{{ $label }}</label>
    @endif

    <div class="input-group"> {{-- Wrapper for potential icon --}}
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes->merge(['class' => 'form-control']) }} {{-- Add more default classes if needed --}}
        >
         @if($icon)
            <span class="input-icon"><i class="{{ $icon }}"></i></span>
         @endif
    </div>

    {{-- Display validation errors --}}
    @error($name)
        <span class="invalid-feedback" role="alert" style="color: red; font-size: 0.85em; display: block; margin-top: 4px;">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>