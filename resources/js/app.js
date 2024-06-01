import './bootstrap';


// Sidebar Toggle Function
document.getElementById('sidebarToggleBtn').addEventListener('click', function () {
    document.getElementById('sidebar').classList.toggle('closed');
    document.getElementById('mainContent').classList.toggle('closed');
});

document.getElementById('closeSidebarBtn').addEventListener('click', function () {
    document.getElementById('sidebar').classList.toggle('closed');
    document.getElementById('mainContent').classList.toggle('closed');
});