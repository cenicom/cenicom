@props([
    'label' => null,
    'name',
    'required' => false,
    'help' => null,
])

<div class="cn-field">

    @if($label)

        <label
            for="{{ $name }}"
            class="cn-field__label">

            {{ $label }}

            @if($required)

                <span class="cn-required">*</span>

            @endif

        </label>

    @endif

    {{ $slot }}

    @if($help)

        <small class="cn-field__help">

            {{ $help }}

        </small>

    @endif

    @error($name)

        <div class="cn-field__error">

            {{ $message }}

        </div>

    @enderror

</div>
