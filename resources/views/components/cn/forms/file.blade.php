@props([
    'name',
    'label' => null,
    'accept' => null,
    'multiple' => false,
    'hint' => null,
])

<div class="cn-form-group">

    @if($label)
        <label class="cn-label">
            {{ $label }}
        </label>
    @endif

    <input
        type="file"
        id="{{ $name }}"
        name="{{ $multiple ? $name.'[]' : $name }}"
        @if($accept) accept="{{ $accept }}" @endif
        @if($multiple) multiple @endif

        {{ $attributes->merge([
            'class'=>'cn-file'
        ]) }}
    >

    @if($hint)
        <div class="cn-hint">
            {{ $hint }}
        </div>
    @endif

    @error($name)
        <div class="cn-error">
            {{ $message }}
        </div>
    @enderror

</div>
