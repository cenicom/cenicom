import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

export function initFormConfirmations() {
    const root = document.documentElement;

    if (root.dataset.cnFormConfirmationsInitialized === 'true') {
        return;
    }

    root.dataset.cnFormConfirmationsInitialized = 'true';

    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (!(form instanceof HTMLFormElement)) {
            return;
        }

        const confirmationType = form.dataset.cnConfirm;

        if (!['delete', 'logout'].includes(confirmationType)) {
            return;
        }

        if (form.dataset.cnConfirmBypass === 'true') {
            delete form.dataset.cnConfirmBypass;
            return;
        }

        event.preventDefault();

        const isLogout = confirmationType === 'logout';
        const submitter =
            event.submitter instanceof HTMLElement
                ? event.submitter
                : null;

        Swal.fire({
            title:
                form.dataset.cnConfirmTitle ||
                (isLogout ? '¿Cerrar sesión?' : '¿Confirmar eliminación?'),
            text:
                form.dataset.cnConfirmText ||
                (isLogout
                    ? '¿Deseas salir de CENICOM?'
                    : 'Esta acción no se puede deshacer.'),
            icon: isLogout ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonText: isLogout ? 'Sí, cerrar sesión' : 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            reverseButtons: true,
            focusCancel: true,
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            form.dataset.cnConfirmBypass = 'true';

            if (submitter) {
                form.requestSubmit(submitter);
            } else {
                form.requestSubmit();
            }
        });
    });
}

export function initFlashMessages() {
    const container = document.getElementById('cn-flash-messages');

    if (!container) {
        return;
    }

    const error = container.dataset.cnFlashError?.trim();

    if (!error) {
        return;
    }

    Swal.fire({
        title: 'No se puede eliminar',
        text: error,
        icon: 'error',
        confirmButtonText: 'Entendido',
    });
}