@props([
    'total' => 0,
    'from' => 0,
    'to' => 0,
    'perPage' => 10,
])

<div class="cn-pagination">

    <div class="cn-pagination-info">

        Mostrando

        <strong>{{ $from }}</strong>

        a

        <strong>{{ $to }}</strong>

        de

        <strong>{{ $total }}</strong>

        registros

    </div>

    <div class="cn-pagination-right">

        <select class="cn-pagination-select">

            <option>10</option>

            <option>25</option>

            <option>50</option>

            <option>100</option>

        </select>

        {{ $slot }}

    </div>

</div>
