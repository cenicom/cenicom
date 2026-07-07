@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'placeholder' => 'Seleccione...',
    'optionValue' => 'id',
    'optionLabel' => 'name',
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

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @required($required)
        @disabled($disabled)

        {{ $attributes->merge([
            'class'=>'cn-select'
        ]) }}
    >

        <option value="">
            {{ $placeholder }}
        </option>

        @foreach($options as $option)

            <option
                value="{{ data_get($option,$optionValue) }}"
                @selected(old($name,$value)==data_get($option,$optionValue))
            >

                {{ data_get($option,$optionLabel) }}

            </option>

        @endforeach

    </select>

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
