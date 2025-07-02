document.addEventListener('DOMContentLoaded', function() {
    const btns = document.querySelector('.header-btns');
    const menuToggle = document.getElementById('mobileMenuToggle');

    if (menuToggle && btns) {
        menuToggle.addEventListener('click', () => {
        btns.classList.toggle('hidden');
        });
    }
    });