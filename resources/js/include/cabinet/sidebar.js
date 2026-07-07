export function cabinetSidebarInit() {
    const toggle = document.querySelector('.sb-toggle');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sb-overlay');

    if (!toggle || !sidebar || !overlay) {
        return;
    }

    const close = () => {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    };

    const toggleOpen = () => {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    };

    toggle.addEventListener('click', toggleOpen);
    overlay.addEventListener('click', close);
    sidebar.querySelectorAll('.nav-item').forEach((link) => {
        link.addEventListener('click', close);
    });
}
