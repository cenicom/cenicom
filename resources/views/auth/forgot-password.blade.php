<x-layout.app :title="'Recuperar contraseña'">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-7 col-lg-5">
                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <div class="text-center mb-4">
                            <h1 class="h3 mb-2">Recuperar contraseña</h1>
                            <p class="text-muted mb-0">
                                Ingrese el correo electrónico asociado a su cuenta.
                            </p>
                        </div>

                        @if (session('status') === 'password-reset-link-sent')
                            <div class="alert alert-success" role="status">
                                Si el correo corresponde a una cuenta registrada,
                                recibirá instrucciones para restablecer su contraseña.
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('password.email') }}">
                            @csrf

                            <div class="mb-4">
                                <x-cn.forms.input
                                    name="email"
                                    id="email"
                                    type="email"
                                    label="Correo electrónico"
                                    placeholder="Ingrese su correo electrónico"
                                    :value="old('email')"
                                    :required="true"
                                    :readonly="false"
                                    :maxlength="null"
                                    :step="null"
                                    autocomplete="email"
                                    autofocus
                                />
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Enviar instrucciones
                            </button>
                        </form>

                        <div class="mt-3 text-center">
                            <a href="{{ route('login') }}">
                                Volver al inicio de sesión
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layout.app>