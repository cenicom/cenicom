<aside class="cds-sidebar">

    {{-- ============================================================
        HEADER
    ============================================================= --}}
    <div class="cds-sidebar-header">

        <div class="cds-sidebar-logo-wrapper">

            <img
                src="{{ asset('assets/images/logo.png') }}"
                alt="CENICOM"
                class="cds-sidebar-logo">

        </div>

        <div class="cds-sidebar-brand">

            <h1>CENICOM</h1>

            <span>Enterprise Suite</span>

        </div>

    </div>

    {{-- ============================================================
        MENU
    ============================================================= --}}
    <nav class="cds-sidebar-menu">

        @foreach($navigation as $item)

            @if(isset($item['header']))

                <div class="cds-sidebar-group">

                    {{ $item['header'] }}

                </div>

            @else

                @php

                    $route = $item['route'] ?? '#';

                    $hasRoute = $route !== '#'
                        && Route::has($route);

                    $isActive = $hasRoute
                        && request()->routeIs($route);

                    $hasChildren = !empty($item['children']);

                @endphp

                <a
                    href="{{ $hasRoute ? route($route) : '#' }}"
                    class="cds-sidebar-item {{ $isActive ? 'active' : '' }}">

                    <span class="cds-sidebar-icon">

                        <i class="fas fa-{{ $item['icon'] }}"></i>

                    </span>

                    <span class="cds-sidebar-text">

                        {{ $item['title'] }}

                    </span>

                    @if($hasChildren)

                        <span class="cds-sidebar-arrow">

                            <i class="fas fa-chevron-right"></i>

                        </span>

                    @endif

                </a>

            @endif

        @endforeach

    </nav>

    {{-- ============================================================
        FOOTER
    ============================================================= --}}
    <div class="cds-sidebar-footer">

        <strong>CENICOM</strong>

        <small>Enterprise Suite</small>

        <small>Versión 1.0.0</small>

        <small>Laravel 13</small>

    </div>

</aside>
