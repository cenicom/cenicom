<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CN DataTables Audit</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="cn-page">
        <h1>CN DataTables Audit</h1>

        <table
            id="datatable-audit"
            class="table table-striped"
            data-cn-datatable
        >
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Código</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>1</td>
                    <td>Dólar estadounidense</td>
                    <td>USD</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Euro</td>
                    <td>EUR</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Peso colombiano</td>
                    <td>COP</td>
                </tr>
            </tbody>
        </table>
    </main>
</body>
</html>
