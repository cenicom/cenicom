document.addEventListener('DOMContentLoaded', () => {

    const toggle = document.getElementById('sidebarToggle');
    const body = document.body;

    if (toggle) {
        toggle.addEventListener('click', () => {
            body.classList.toggle('sidebar-collapse');
        });
    }

});
