const navbar = document.getElementById('navbar');

document.addEventListener('scroll', () => {
    if (window.scrollY > navbar.offsetHeight) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});
