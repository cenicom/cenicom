@php
    $modalAttributes = $attributes->merge([
        'class' => 'modal fade cn-modal',
        'id' => $id,
        'tabindex' => '-1',
        'aria-hidden' => 'true',
    ]);

    $dialogClass = match ($size) {
        'sm' => 'modal-sm',
        'lg' => 'modal-lg',
        'xl' => 'modal-xl',
        default => '',
    };
@endphp

<div {{ $modalAttributes }}>

    <div class="modal-dialog {{ $dialogClass }}">

        <div class="modal-content">

            @isset($header)
                <div class="modal-header">
                    {{ $header }}
                </div>
            @elseif($title)
                <div class="modal-header">

                    <h5 class="modal-title">
                        {{ $title }}
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Cerrar"
                    ></button>

                </div>
            @endisset


            <div class="modal-body">

                {{ $body ?? $slot }}

            </div>


            @isset($footer)

                <div class="modal-footer">

                    {{ $footer }}

                </div>

            @endisset

        </div>

    </div>

</div>
