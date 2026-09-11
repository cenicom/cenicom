import jQuery from 'jquery';
import DataTable from 'datatables.net-bs5';

import 'datatables.net-buttons-bs5';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';

import 'datatables.net-responsive-bs5';

import JSZip from 'jszip';
import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';

window.jQuery = jQuery;
window.$ = jQuery;

pdfMake.addVirtualFileSystem(pdfFonts);

DataTable.Buttons.jszip(JSZip);
DataTable.Buttons.pdfMake(pdfMake);

const BUTTONS = [
    {
        extend: 'copy',
        text: '<i class="fas fa-copy"></i> Copiar',
        titleAttr: 'Copiar',
    },
    {
        extend: 'excel',
        text: '<i class="fas fa-file-excel"></i> Excel',
        titleAttr: 'Exportar a Excel',
    },
    {
        extend: 'csv',
        text: '<i class="fas fa-file-csv"></i> CSV',
        titleAttr: 'Exportar a CSV',
    },
    {
        extend: 'pdf',
        text: '<i class="fas fa-file-pdf"></i> PDF',
        titleAttr: 'Exportar a PDF',
    },
    {
        extend: 'print',
        text: '<i class="fas fa-print"></i> Imprimir',
        titleAttr: 'Imprimir',
    },
];

export function initDataTables(root = document) {
    const tables = root.querySelectorAll(
        'table[data-cn-datatable]'
    );

    tables.forEach((table) => {
        if (DataTable.isDataTable(table)) {
            return;
        }

        new DataTable(table, {
            responsive: true,

            paging: false,
            info: false,
            lengthChange: false,

            layout: {
                topStart: {
                    buttons: BUTTONS,
                },

                topEnd: {
                    search: {
                        placeholder: 'Buscar...',
                    },
                },
            },

            language: {
                search: '',
                zeroRecords: 'No se encontraron registros.',
                emptyTable: 'No existen registros.',
            },
        });
    });
}
