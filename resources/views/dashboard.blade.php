<x-layout.app>
    <x-slot:title>
        Dashboard
    </x-slot:title>

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

                        <li
                            class="breadcrumb-item active"
                            aria-current="page"
                        >
                            Dashboard
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <section
            class="mt-4"
            aria-labelledby="institution-summary-title"
        >
            <div class="card">
                <div class="card-header">
                    <h2
                        id="institution-summary-title"
                        class="h5 mb-0"
                    >
                        Instituciones
                    </h2>
                </div>

                <div class="card-body">
                    <div class="fs-2 fw-semibold">
                        {{ $totalInstitutions }}
                    </div>

                    <div class="text-muted">
                        Total de instituciones registradas
                    </div>
                </div>
            </div>
        </section>

        <section
            class="mt-4"
            aria-labelledby="recent-activities-title"
        >
            <div class="card">

                <div class="card-header">
                    <h2
                        id="recent-activities-title"
                        class="h5 mb-0"
                    >
                        Actividades recientes
                    </h2>
                </div>

                <div class="card-body">

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

                </div>

            </div>
        </section>

    </div>
</x-layout.app>
