@props([
    'title' => __('Sin registros'),
    'message' => __('No existen datos para mostrar.'),
    'icon' => 'database',
])

<div class="cn-empty">

    <i class="fas fa-{{ $icon }}"></i>

    <h3>{{ $title }}</h3>

    <p>{{ $message }}</p>

</div>
