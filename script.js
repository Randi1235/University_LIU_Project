// Loader Animation
window.addEventListener('load', function() {
    const loader = document.getElementById('loader');
    if (loader) {
        loader.style.opacity = '0';
        setTimeout(() => loader.style.display = 'none', 400);
    }
});
// Scroll to Top Button
document.addEventListener('DOMContentLoaded', function() {
    const scrollBtn = document.getElementById('scrollTopBtn');
    if (scrollBtn) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollBtn.classList.add('show');
            } else {
                scrollBtn.classList.remove('show');
            }
        });
        scrollBtn.addEventListener('click', function() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
// Lightbox pour la galerie d'images
document.addEventListener('DOMContentLoaded', function() {
    const galleryImgs = document.querySelectorAll('.gallery-img');
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightboxImg');
    const lightboxClose = document.getElementById('lightboxClose');
    if (galleryImgs.length && lightbox && lightboxImg && lightboxClose) {
        galleryImgs.forEach(img => {
            img.addEventListener('click', function() {
                lightboxImg.src = img.src;
                lightbox.classList.add('open');
            });
        });
        lightboxClose.addEventListener('click', function() {
            lightbox.classList.remove('open');
            lightboxImg.src = '';
        });
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                lightbox.classList.remove('open');
                lightboxImg.src = '';
            }
        });
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                lightbox.classList.remove('open');
                lightboxImg.src = '';
            }
        });
    }
});
// FAQ Accordion
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', function() {
            const item = btn.closest('.faq-item');
            const expanded = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', !expanded);
            item.classList.toggle('open');
        });
    });
});
// Carousel Témoignages
document.addEventListener('DOMContentLoaded', function() {
    const testimonials = document.querySelectorAll('.testimonial');
    const prevBtn = document.getElementById('prevTestimonial');
    const nextBtn = document.getElementById('nextTestimonial');
    let current = 0;
    function showTestimonial(idx) {
        testimonials.forEach((t, i) => t.classList.toggle('active', i === idx));
    }
    if (prevBtn && nextBtn && testimonials.length) {
        prevBtn.addEventListener('click', function() {
            current = (current - 1 + testimonials.length) % testimonials.length;
            showTestimonial(current);
        });
        nextBtn.addEventListener('click', function() {
            current = (current + 1) % testimonials.length;
            showTestimonial(current);
        });
    }
});
// Menu hamburger et interactions avec animations
document.addEventListener('DOMContentLoaded', function() {
    const hamburgerBtn = document.getElementById('hamburgerBtn');
    const navLinks = document.getElementById('navLinks');

    if (hamburgerBtn && navLinks) {
        // Toggle menu hamburger
        hamburgerBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            navLinks.classList.toggle('open');
            hamburgerBtn.classList.toggle('active');
        });

        // Fermer le menu quand on clique sur un lien
        const navItems = navLinks.querySelectorAll('a');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                navLinks.classList.remove('open');
                hamburgerBtn.classList.remove('active');
            });
        });
    }

    // Fermer le menu si on clique en dehors
    document.addEventListener('click', function(event) {
        if (navLinks && navLinks.classList.contains('open')) {
            if (!navLinks.contains(event.target) && event.target !== hamburgerBtn) {
                navLinks.classList.remove('open');
                if (hamburgerBtn) hamburgerBtn.classList.remove('active');
            }
        }
    });

    // Smooth scroll pour les liens internes
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});
