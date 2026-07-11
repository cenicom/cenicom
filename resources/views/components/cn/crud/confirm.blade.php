<x-cn.modal
    :id="$id"
    :title="$title">

    <x-slot:body>

        <p class="cn-confirm-message">
            {{ $message }}
        </p>

    </x-slot:body>


    <x-slot:footer>

        <button
            type="button"
            class="btn btn-secondary"
            data-bs-dismiss="modal">

            {{ $cancelText }}

        </button>


        {{ $slot }}


    </x-slot:footer>

</x-cn.modal>
