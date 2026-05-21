document.addEventListener('DOMContentLoaded', () => {
    console.log('admit.js loaded');

    // Animation Handler
    function handleAnimations() {
        const elements = document.querySelectorAll('[data-animate]');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const animation = element.getAttribute('data-animate');
                    const delay = element.getAttribute('data-delay') || '0s';
                    element.classList.add('animate__animated', 'animate__' + animation);
                    element.style.animationDelay = delay;
                    observer.unobserve(element);
                }
            });
        }, { threshold: 0.2 });

        elements.forEach(element => observer.observe(element));

        // Handle hover animations
        const hoverElements = document.querySelectorAll('[data-animate-hover]');
        hoverElements.forEach(element => {
            element.addEventListener('mouseenter', () => {
                const animation = element.getAttribute('data-animate-hover');
                element.classList.add('animate__animated', 'animate__' + animation);
                element.addEventListener('animationend', () => {
                    element.classList.remove('animate__animated', 'animate__' + animation);
                }, { once: true });
            });
        });
    }

    // Back to top button
    const backToTopButton = document.querySelector('.back-to-top');
    if (backToTopButton) {
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('show');
            } else {
                backToTopButton.classList.remove('show');
            }
        });

        backToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Form submission handler
    const form = document.getElementById('admitForm');
    const downloadButton = document.getElementById('downloadButton');
    if (form && downloadButton) {
        form.addEventListener('submit', (e) => {
            if (!form.checkValidity()) {
                e.preventDefault();
                form.classList.add('was-validated');
                return;
            }
            downloadButton.disabled = true;
            downloadButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';
            setTimeout(() => {
                downloadButton.disabled = false;
                downloadButton.innerHTML = '<i class="fas fa-download me-2"></i>Download Admit Card';
            }, 2000);
        });
    }

    // Initialize animations
    handleAnimations();
});