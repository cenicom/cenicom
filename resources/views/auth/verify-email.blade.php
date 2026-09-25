<x-layout.app :title="'Verificar correo electrónico'">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-7 col-lg-5">

                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <div class="text-center mb-4">
                            <h1 class="h3 mb-2">Verifica tu dirección de correo</h1>
                            <p class="text-muted mb-0">
                                Antes de continuar, verifica tu dirección de correo electrónico
                                utilizando el enlace que enviamos a tu correo.
                            </p>
                        </div>

                        @if (session('status') === 'verification-link-sent')
                            <div class="alert alert-success" role="alert">
                                Se ha enviado un nuevo enlace de verificación a tu dirección de correo.
                            </div>
                        @endif

                        <form method="POST" action="{{ route('verification.send') }}" class="mb-3">
                            @csrf

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Reenviar enlace de verificación
                            </button>
                        </form>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <button
                                type="submit"
                                class="btn btn-link px-0"
                            >
                                Cerrar sesión
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</x-layout.app>
