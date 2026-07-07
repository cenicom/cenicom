@props([
    'name',
    'label' => null,
    'rows' => 4,
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'disabled' => false,
    'hint' => null,
])

<div class="cn-form-group">

    @if($label)
        <label for="{{ $name }}" class="cn-label">
            {{ $label }}

            @if($required)
                <span class="cn-required">*</span>
            @endif
        </label>
    @endif

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        @required($required)
        @disabled($disabled)
        {{ $attributes->merge(['class'=>'cn-textarea']) }}
    >{{ old($name,$value) }}</textarea>

    @error($name)
        <div class="cn-error">{{ $message }}</div>
    @else
        @if($hint)
            <div class="cn-hint">{{ $hint }}</div>
        @endif
    @enderror

</div>
