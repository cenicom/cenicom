{{-- ==========================================================
 | CENICOM ERP
 | Scripts globales del sistema
 ========================================================== --}}

<script>

    window.Cenicom = {

        appName: "{{ config('app.name') }}",

        locale: "{{ app()->getLocale() }}",

        csrfToken: "{{ csrf_token() }}",

    };

</script>

@stack('scripts')