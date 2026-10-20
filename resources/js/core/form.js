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

        if (!['create', 'delete', 'logout'].includes(confirmationType)) {
            return;
        }

        if (form.dataset.cnConfirmBypass === 'true') {
            delete form.dataset.cnConfirmBypass;
            return;
        }

        event.preventDefault();

        const isLogout = confirmationType === 'logout';
        const isCreate = confirmationType === 'create';
        const submitter =
            event.submitter instanceof HTMLElement
                ? event.submitter
                : null;

        Swal.fire({
            title:
                form.dataset.cnConfirmTitle ||
                (isLogout
                    ? '¿Cerrar sesión?'
                    : isCreate
                      ? '¿Guardar esta institución?'
                      : '¿Confirmar eliminación?'),
            text:
                form.dataset.cnConfirmText ||
                (isLogout
                    ? '¿Deseas salir de CENICOM?'
                    : isCreate
                      ? '¿Confirmas que deseas crear la institución con los datos ingresados?'
                      : 'Esta acción no se puede deshacer.'),
            icon: isLogout || isCreate ? 'question' : 'warning',
            showCancelButton: true,
            confirmButtonText: isLogout
                ? 'Sí, cerrar sesión'
                : isCreate
                  ? 'Sí, guardar'
                  : 'Sí, eliminar',
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

    if (error) {
        Swal.fire({
            title: 'No se puede eliminar',
            text: error,
            icon: 'error',
            confirmButtonText: 'Entendido',
        });

        return;
    }

    const success = container.dataset.cnFlashSuccess?.trim();

    if (!success) {
        return;
    }

    Swal.fire({
        title: 'Operación exitosa',
        text: success,
        icon: 'success',
        confirmButtonText: 'Entendido',
    });
}
