@extends('layouts.app')

@section('content')

<h2>Editar País</h2>

<form action="{{ route('countries.update', $country) }}" method="POST">
    @csrf
    @method('PUT')

    @include('countries.form')

    <button class="btn btn-primary">Actualizar</button>
    <a href="{{ route('countries.index') }}" class="btn btn-secondary">Volver</a>
</form>

@endsection
