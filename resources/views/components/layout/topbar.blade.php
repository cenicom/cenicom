<header class="cn-topbar">

    <div class="cn-topbar-left">

        <button
            id="sidebarToggle"
            class="cn-toggle">

            <i class="fas fa-bars"></i>

        </button>

        <img
            src="{{ asset('assets/images/logo.png') }}"
            alt="CENICOM"
            class="cn-logo">

    </div>

    <div class="cn-topbar-right">

        <div class="cn-icon">

            <i class="fas fa-search"></i>

        </div>

        <div class="cn-icon">

            <i class="far fa-bell"></i>

        </div>

        <div class="cn-icon">

            <i class="fas fa-moon"></i>

        </div>

        <div class="dropdown">

            <a
                href="#"
                class="text-dark text-decoration-none dropdown-toggle"
                data-bs-toggle="dropdown">

                {{ auth()->user()->name ?? 'Invitado' }}

            </a>

            <ul class="dropdown-menu dropdown-menu-end">

                <li>

                    <a class="dropdown-item" href="#">

                        Mi perfil

                    </a>

                </li>

                <li>

                    <a class="dropdown-item" href="#">

                        Configuración

                    </a>

                </li>

                <li><hr class="dropdown-divider"></li>

                <li>

                    <a
                        class="dropdown-item text-danger"
                        href="#">

                        Cerrar sesión

                    </a>

                </li>

            </ul>

        </div>

    </div>

</header>