(function($) {
    'use strict';

    document.addEventListener("DOMContentLoaded", function() {
        // Initialize required GSAP plugins
        gsap.registerPlugin(ScrollTrigger);

        // Get settings from PHP
        const settings = window.aggSettings || {
            animation_type: 'fade-up',
            animation_style: 'group',
            animation_duration: 2,
            hover_effect: 'zoom'
        };

        // Only initialize Lenis if we find a gallery
        const hasGallery = document.querySelector('.wp-block-gallery.agg-animated');
        let lenis = null;

        if (hasGallery) {
            lenis = new Lenis({
                duration: 1.2,
                easing: (t) => (t === 1 ? 1 : 1 - Math.pow(2, -10 * t)),
                smoothWheel: true,
                wheelMultiplier: 1,
                touchMultiplier: 2,
            });

            function raf(time) {
                lenis.raf(time);
                requestAnimationFrame(raf);
            }

            requestAnimationFrame(raf);
            lenis.on('scroll', ScrollTrigger.update);
        }

        // Initialize lazy load observer
        const lazyImageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        
                        img.onload = () => {
                            const figure = img.closest('figure.wp-block-image');
                            if (figure) {
                                initializeGalleryItem(figure);
                            }
                        };
                    }
                    observer.unobserve(img);
                }
            });
        });

        // Initialize lightbox functionality
        function setupLightbox() {
            if (!document.getElementById('aggGallery')) {
                const lightboxHtml = `
                    <div id="aggGallery">
                        <div id="aggGallery-content">
                            <img id="aggGallery-image" src="" alt="">
                            <span id="aggGallery-close">&times;</span>
                            <span id="aggGallery-prev">&#10094;</span>
                            <span id="aggGallery-next">&#10095;</span>
                        </div>
                    </div>
                `;
                document.body.insertAdjacentHTML('beforeend', lightboxHtml);
            }

            const modal = document.getElementById('aggGallery');
            const modalContent = document.getElementById('aggGallery-content');
            const modalImg = document.getElementById('aggGallery-image');
            const closeBtn = document.getElementById('aggGallery-close');
            const prevBtn = document.getElementById('aggGallery-prev');
            const nextBtn = document.getElementById('aggGallery-next');
            let currentGallery = null;
            let currentIndex = 0;

            function showLightbox() {
                modal.style.display = 'flex';
                gsap.fromTo(modal, 
                    { opacity: 0 }, 
                    { opacity: 1, duration: 0.5 }
                );
            }

            function hideLightbox() {
                gsap.to(modal, {
                    opacity: 0,
                    duration: 0.5,
                    onComplete: () => {
                        modal.style.display = 'none';
                    }
                });
            }

            function getLargestImageURL(img) {
                if (!img.srcset) return img.src;
                const sources = img.srcset.split(',')
                    .map(src => {
                        const [url, width] = src.trim().split(' ');
                        return { url, width: parseInt(width) };
                    })
                    .sort((a, b) => b.width - a.width);
                return sources[0].url;
            }

            function navigateImage(direction) {
                if (!currentGallery) return;
                const images = currentGallery.querySelectorAll('figure.wp-block-image img');
                currentIndex = (currentIndex + direction + images.length) % images.length;
                modalImg.src = getLargestImageURL(images[currentIndex]);
            }

            const lightboxGalleries = document.querySelectorAll('.wp-block-gallery.agg-lightbox');
            lightboxGalleries.forEach(gallery => {
                const images = gallery.querySelectorAll('figure.wp-block-image img');
                images.forEach((img, index) => {
                    img.style.cursor = 'pointer';
                    img.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentGallery = gallery;
                        currentIndex = index;
                        modalImg.src = getLargestImageURL(img);
                        showLightbox();
                    });
                });
            });

            closeBtn.onclick = hideLightbox;
            modal.onclick = (e) => {
                if (e.target === modal || e.target === modalContent) hideLightbox();
            };
            
            prevBtn.onclick = (e) => {
                e.stopPropagation();
                navigateImage(-1);
            };
            
            nextBtn.onclick = (e) => {
                e.stopPropagation();
                navigateImage(1);
            };

            document.addEventListener('keydown', (e) => {
                if (modal.style.display === 'none') return;
                
                switch(e.key) {
                    case 'ArrowLeft':
                        navigateImage(-1);
                        break;
                    case 'ArrowRight':
                        navigateImage(1);
                        break;
                    case 'Escape':
                        hideLightbox();
                        break;
                }
            });
        }
        setupLightbox();

        function initializeGalleryItem(figure, index = 0) {
            // Apply animation and hover effect
            AGGAnimations.applyAnimation(figure, {
                type: settings.animation_type,
                style: settings.animation_style,
                duration: settings.animation_duration,
                index: index,
                hoverEffect: settings.hover_effect,
                isAdmin: false
            });
        }

        // Initialize galleries
        const animatedGalleries = document.querySelectorAll('.wp-block-gallery.agg-animated');
        animatedGalleries.forEach(gallery => {
            const figures = gallery.querySelectorAll('figure.wp-block-image');
            
            figures.forEach((figure, index) => {
                const img = figure.querySelector('img');
                
                if (img) {
                    if (img.complete && img.naturalHeight !== 0) {
                        initializeGalleryItem(figure, index);
                    } else if (img.dataset.src) {
                        lazyImageObserver.observe(img);
                    } else {
                        img.onload = () => initializeGalleryItem(figure, index);
                    }
                }
            });
        });

        // Handle browser back/forward cache
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                location.reload();
            }
        });
    });
})(jQuery);