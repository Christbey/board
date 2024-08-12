import './bootstrap';
import 'chart.js/auto';

document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggleBtn = document.getElementById('sidebarToggleBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');

    if (sidebarToggleBtn) {
        sidebarToggleBtn.addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('closed');
            document.getElementById('mainContent').classList.toggle('closed');
        });
    }

    if (closeSidebarBtn) {
        closeSidebarBtn.addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('closed');
            document.getElementById('mainContent').classList.toggle('closed');
        });
    }
});
