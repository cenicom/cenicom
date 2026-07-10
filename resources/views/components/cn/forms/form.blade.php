@php
    $httpMethod = strtoupper($method);

    $formAttributes = $attributes->merge([
        'class' => 'cn-form',
    ]);
@endphp

<form
    action="{{ $action }}"
    method="{{ in_array($httpMethod, ['GET', 'POST']) ? $httpMethod : 'POST' }}"

    @if($id)
        id="{{ $id }}"
    @endif

    @if($autocomplete)
        autocomplete="{{ $autocomplete }}"
    @endif

    @if($novalidate)
        novalidate
    @endif

    {{ $formAttributes }}
>

    @unless($httpMethod === 'GET')
        @csrf
    @endunless

    @if(! in_array($httpMethod, ['GET', 'POST']))
        @method($httpMethod)
    @endif

    {{ $slot }}

</form>
