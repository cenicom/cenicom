<div class="cn-field">

    @if($label)

        <label class="form-label">

            {{ $label }}

            @if($required)

                <span class="text-danger">
                    *
                </span>

            @endif

        </label>

    @endif


    <div class="cn-field-control">

        {{ $slot }}

    </div>


    @if($help)

        <small class="form-text text-muted">

            {{ $help }}

        </small>

    @endif


    @if($error)

        <small class="text-danger">

            {{ $error }}

        </small>

    @endif


</div>
