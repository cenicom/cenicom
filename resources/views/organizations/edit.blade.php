@extends('layouts.app')

@section('title', 'Nueva Organización')

@section('content')

    <x-ui.page-header title="Nueva Organización" subtitle="Registrar una nueva organización" icon="building">

        <x-slot:actions>

            <x-ui.button :href="route('organizations.index')" color="secondary" icon="arrow-left">

                Volver

            </x-ui.button>

        </x-slot:actions>

    </x-ui.page-header>

    <x-ui.card title="Información General">

        <form action="{{ route('organizations.update', $organization) }}" method="POST">

            @csrf
            @method('PUT')

            @include('organizations.partials.form')

            <div class="text-end mt-4">

                <x-ui.button :href="route('organizations.index')" color="secondary">

                    Cancelar

                </x-ui.button>

                <x-ui.button type="submit" color="success" icon="floppy-disk">

                    Actualizar

                </x-ui.button>

            </div>

        </form>

    </x-ui.card>

@endsection
