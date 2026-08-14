<header class="cn-topbar">

    <div class="cn-topbar-content">

        <div class="cn-topbar-brand">
            {{ config('app.name') }}
        </div>

        <div class="cn-topbar-actions">
            {{ $slot ?? '' }}
        </div>

    </div>

</header>