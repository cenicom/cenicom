<div {{ $attributes->merge(['class' => 'cn-display']) }}>

    @if(isset($value))

        {{ $value }}

    @else

        {{ $slot }}

    @endif

</div>
