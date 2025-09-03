// auth.js
function logout() {
    localStorage.removeItem('auth_token');
    window.location.href = '/login';
}

// Check authentication on page load
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('auth_token');
    if (!token) {
        window.location.href = '/login';
    }
});