/**
 * Simple script to give navbar a line at the bottom when scrolling down the page.
 */

const navbar = document.getElementById('navbar');

document.addEventListener('scroll', () => {
    if (window.scrollY > navbar.offsetHeight) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});
