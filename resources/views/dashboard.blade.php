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

        <section class="mt-4" aria-labelledby="institution-summary-title">
            <x-cn.card>

                <x-cn.card.header id="institution-summary-title" title="Instituciones" icon="building" />

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

        <section class="mt-4" aria-labelledby="recent-activities-title">
            <x-cn.card>

                <x-cn.card.header id="recent-activities-title" title="Actividades recientes" icon="history" />

                <x-cn.card.body>

                    @forelse ($activities as $activity)

                        <article class="border-bottom py-3">

                            <div class="d-flex justify-content-between align-items-start gap-3">

                                <div>
                                    <div class="fw-semibold">
                                        {{ $activity->eventType }}
                                    </div>

                                    <div class="text-muted">
                                        {{ $activity->actorName }}
                                    </div>
                                </div>

                                <time class="text-muted small text-nowrap"
                                    datetime="{{ $activity->occurredAt->format('c') }}">
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
