━━━━━━━━━━━━━━━━━━━━━━

DESARROLLO

🎨 CENICOM Design System

━━━━━━━━━━━━━━━━━━━━━━
<aside class="cn-sidebar">

    <div class="cn-sidebar-header">

        <span>MENÚ PRINCIPAL</span>

    </div>

    <nav class="cn-sidebar-menu">

        @foreach($navigation as $item)

            {{-- Encabezados --}}
            @if(isset($item['header']))

                <div class="cn-sidebar-group">

                    {{ $item['header'] }}

                </div>

            @else

                @php
                    $isActive = isset($item['route'])
                        && $item['route'] !== '#'
                        && Route::has($item['route'])
                        && request()->routeIs($item['route']);
                @endphp

                <a
                    href="{{ isset($item['route']) && Route::has($item['route']) ? route($item['route']) : '#' }}"
                    class="cn-sidebar-item {{ $isActive ? 'active' : '' }}">

                    <span class="cn-sidebar-icon">

                        <i class="fas fa-{{ $item['icon'] }}"></i>

                    </span>

                    <span class="cn-sidebar-text">

                        {{ $item['title'] }}

                    </span>

                </a>

            @endif

        @endforeach

    </nav>

</aside>
