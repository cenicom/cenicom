<x-layout.app>
    <x-slot name="title">
        Dashboard
    </x-slot>

    <div class="cn-page">

        <div class="cn-page-header">
            <div>
                <h1 class="cn-page-title">
                    Dashboard
                </h1>

                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            Inicio
                        </li>

                        <li class="breadcrumb-item active" aria-current="page">
                            Dashboard
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row g-3 mt-3">

            <section class="col-12 col-md-6" aria-labelledby="institution-summary-title">
                <x-cn.card>
                    <x-cn.card.header
                        id="institution-summary-title"
                        title="Instituciones"
                        icon="building"
                    />

                    <x-cn.card.body>
                        <div class="fs-2 fw-semibold">
                            {{ $totalInstitutions }}
                        </div>

                        <div class="text-muted">
                            Total de instituciones registradas
                        </div>
                    </x-cn.card.body>
                </x-cn.card>
            </section>

            <section class="col-12 col-md-6" aria-labelledby="campus-summary-title">
                <x-cn.card>
                    <x-cn.card.header
                        id="campus-summary-title"
                        title="Sedes"
                        icon="building"
                    />

                    <x-cn.card.body>
                        <div class="fs-2 fw-semibold">
                            {{ $totalCampuses }}
                        </div>

                        <div class="text-muted">
                            Total de sedes registradas
                        </div>
                    </x-cn.card.body>
                </x-cn.card>
            </section>

        </div>

        <section class="mt-4" aria-labelledby="recent-activities-title">
            <x-cn.card>
                <x-cn.card.header
                    id="recent-activities-title"
                    title="Actividades recientes"
                    icon="history"
                />

                <x-cn.card.body>
                    @forelse ($activities as $activity)
                        <article class="border-bottom py-3">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="fw-semibold">
                                        {{ $activity->eventType }}
                                    </div>

                                    @if (is_string($activity->data['name'] ?? null) && $activity->data['name'] !== '')
                                        <div>
                                            {{ $activity->data['name'] }}
                                        </div>
                                    @endif

                                    <div class="text-muted">
                                        Por: {{ $activity->actorName }}
                                    </div>

                                    <div class="text-muted small">
                                        Resultado: {{ $activity->result }}
                                    </div>
                                </div>

                                <time
                                    class="text-muted small text-nowrap"
                                    datetime="{{ $activity->occurredAt->format('c') }}"
                                >
                                    {{ $activity->occurredAt->format('d/m/Y H:i') }}
                                </time>
                            </div>
                        </article>
                    @empty
                        <div class="text-muted">
                            No hay actividades recientes.
                        </div>
                    @endforelse
                </x-cn.card.body>
            </x-cn.card>
        </section>

    </div>
</x-layout.app>
