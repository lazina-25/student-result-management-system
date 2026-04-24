const themeBtn = document.getElementById('themeBtn');
const html = document.documentElement;
const savedTheme = localStorage.getItem('theme') || 'light';
html.setAttribute('data-theme', savedTheme);
updateIcon(savedTheme);
function toggleTheme() {
    const current = html.getAttribute('data-theme');
    const newTheme = current === 'light' ? 'dark' : 'light';
    html.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    updateIcon(newTheme);
}
function updateIcon(theme) {
    if(themeBtn) {
        themeBtn.innerHTML = theme === 'light' ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
    }
}
function confirmDelete(url) {
    if(confirm("Are you sure? This action is permanent!")) {
        window.location.href = url;
    }
}