@extends('layouts.app')

@section('content')

<h2>Crear País</h2>

<form action="{{ route('countries.store') }}" method="POST">
    @csrf

    @include('countries.form')

    <button class="btn btn-success">Guardar</button>
    <a href="{{ route('countries.index') }}" class="btn btn-secondary">Volver</a>
</form>

@endsection
