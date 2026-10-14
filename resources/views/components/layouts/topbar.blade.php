<header class="cn-topbar">
    <div class="cn-topbar-content">
        <div class="cn-topbar-brand">
            <button
                type="button"
                class="cn-sidebar-toggle"
                data-cn-sidebar-toggle
                aria-label="Ocultar menú lateral"
                aria-controls="cn-sidebar"
                aria-expanded="true"
                title="Mostrar u ocultar menú lateral"
            >
                <i class="fa-solid fa-bars" aria-hidden="true"></i>
            </button>

            <img
                class="cn-topbar-logo"
                src="{{ asset('assets/images/logo.png') }}"
                alt="CENICOM"
            >

            <span class="cn-topbar-title">
                {{ config('app.name', 'CENICOM') }}
            </span>
        </div>

        <div class="cn-topbar-actions">
            @auth
                <div class="cn-topbar-user">
                    <i class="fa-solid fa-circle-user" aria-hidden="true"></i>
                    <span>
                        {{ auth()->user()->name ?: auth()->user()->email }}
                    </span>
                </div>

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                    data-cn-confirm="logout"
                    data-cn-confirm-title="¿Cerrar sesión?"
                    data-cn-confirm-text="¿Deseas salir de CENICOM?"
                >
                    @csrf

                    <button type="submit" class="cn-logout-button">
                        <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                        <span>Salir</span>
                    </button>
                </form>
            @endauth

            {{ $slot ?? '' }}
        </div>
    </div>
</header>