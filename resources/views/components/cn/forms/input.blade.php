@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'icon' => null,
    'hint' => null,
    'required' => false,
    'disabled' => false,
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

    <div class="cn-input-wrapper">

        @if($icon)
            <span class="cn-input-icon">
                <i class="fas fa-{{ $icon }}"></i>
            </span>
        @endif

        <input
            id="{{ $name }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            @required($required)
            @disabled($disabled)

            {{ $attributes->merge([
                'class' => 'cn-input'.($icon ? ' has-icon' : '')
            ]) }}
        >

    </div>

    @error($name)
        <div class="cn-error">
            {{ $message }}
        </div>
    @else

        @if($hint)

            <div class="cn-hint">
                {{ $hint }}
            </div>

        @endif

    @enderror

</div>
