@extends('layouts.app')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h2>Países</h2>

        <a href="{{ route('countries.create') }}" class="btn btn-primary">
            Nuevo País
        </a>
    </div>

    <table class="table table-bordered table-hover" id="countriesTable">
        <thead>
            <tr>
                <th>Código</th>
                <th>ISO3</th>
                <th>Nombre</th>
                <th>Nacionalidad</th>
                <th>Estado</th>
                <th width="180">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($countries as $country)
                <tr>
                    <td>{{ $country->code }}</td>
                    <td>{{ $country->iso3 }}</td>
                    <td>{{ $country->name }}</td>
                    <td>{{ $country->nationality }}</td>
                    <td>
                        @if ($country->active)
                            <span class="badge bg-success">Activo</span>
                        @else
                            <span class="badge bg-danger">Inactivo</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('countries.edit', $country) }}" class="btn btn-sm btn-warning">Editar</a>

                        <form action="{{ route('countries.destroy', $country) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')

                            <button class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar país?')">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#countriesTable').DataTable({
                pageLength: 10,
                ordering: true,
                searching: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.8/i18n/es-ES.json"
                }
            });
        });
    </script>
@endpush
