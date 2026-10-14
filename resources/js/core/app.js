import { initDataTables } from './datatable.js';
import {
    initFlashMessages,
    initFormConfirmations,
} from './form.js';

function initSidebarToggle() {
    const app = document.querySelector('.cn-app');
    const toggle = document.querySelector('[data-cn-sidebar-toggle]');
    const sidebar = document.getElementById('cn-sidebar');

    if (!app || !toggle || !sidebar) {
        return;
    }

    toggle.addEventListener('click', () => {
        const collapsed = app.classList.toggle('cn-sidebar-collapsed');

        toggle.setAttribute('aria-expanded', String(!collapsed));
        toggle.setAttribute(
            'aria-label',
            collapsed ? 'Mostrar menú lateral' : 'Ocultar menú lateral',
        );
    });
}

function initializeCore() {
    initDataTables();
    initFormConfirmations();
    initFlashMessages();
    initSidebarToggle();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeCore, {
        once: true,
    });
} else {
    initializeCore();
}