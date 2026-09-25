<x-layout.app :title="'Iniciar sesión'">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-7 col-lg-5">

                <div class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        <div class="text-center mb-4">
                            <h1 class="h3 mb-2">Iniciar sesión</h1>
                            <p class="text-muted mb-0">
                                Acceda a CENICOM ERP
                            </p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger" role="alert">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login.authenticate') }}">
                            @csrf

                            <div class="mb-3">
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

                            <div class="mb-4">
                                <x-cn.forms.password
                                    name="password"
                                    id="password"
                                    placeholder="Ingrese su contraseña"
                                    autocomplete="current-password"
                                    :required="true"
                                    autofocus="false"
                                />
                            </div>

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Iniciar sesión
                            </button>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

</x-layout.app>
