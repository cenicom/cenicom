window.Cenicom = window.Cenicom || {};

Cenicom.datatable = {

    init(options = {}) {

        const defaults = {

            responsive: true,

            stateSave: true,

            pageLength: 25,

            ordering: true,

            searching: true,

            language: '/vendor/datatables/es-ES.json'

        };

        const config = {

            ...defaults,

            ...options

        };

        return new DataTable(config.table, config);

    }

};
