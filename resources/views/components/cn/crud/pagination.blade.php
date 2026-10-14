@php
    $paginationAttributes = $attributes->class([
        'cn-pagination',
    ]);
@endphp

@if ($paginator->hasPages())
    <div {{ $paginationAttributes }}>
        {{ $paginator->links('pagination::bootstrap-5') }}
    </div>
@endif
