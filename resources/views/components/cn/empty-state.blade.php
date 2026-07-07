@props([
    'title' => 'No hay registros',
    'description' => 'Cuando exista información aparecerá aquí.',
    'icon' => 'folder-open',
])

<div {{ $attributes->merge(['class' => 'cn-empty']) }}>

    <div class="cn-empty-icon">
        <i class="fas fa-{{ $icon }}"></i>
    </div>

    <h3 class="cn-empty-title">
        {{ $title }}
    </h3>

    <p class="cn-empty-description">
        {{ $description }}
    </p>

    @isset($actions)

        <div class="cn-empty-actions">

            {{ $actions }}

        </div>

    @endisset

</div>
