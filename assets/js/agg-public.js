(function($) {
    'use strict';

    document.addEventListener("DOMContentLoaded", function() {
        // Initialize galleries with fade animation
        const animatedGalleries = document.querySelectorAll('.wp-block-gallery.agg-animated');
        
        // Check if aggSettings is defined and has animation_duration
        const animationDuration = (typeof aggSettings !== 'undefined' && 
                                  typeof aggSettings.animation_duration !== 'undefined') ? 
                                  parseFloat(aggSettings.animation_duration) : 0.5;

        animatedGalleries.forEach(gallery => {
            const figures = gallery.querySelectorAll('figure.wp-block-image');
            
            figures.forEach((figure) => {
                // Set initial state
                gsap.set(figure, { opacity: 0 });
                
                // Create animation with proper duration
                gsap.to(figure, {
                    opacity: 1,
                    duration: animationDuration, // Use the properly defined variable
                    scrollTrigger: {
                        trigger: figure,
                        start: "top bottom+=100",
                        toggleActions: "play none none reverse"
                    }
                });
            });
        });

        // Lightbox functionality
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
            const modalImg = document.getElementById('aggGallery-image');
            const closeBtn = document.getElementById('aggGallery-close');
            const prevBtn = document.getElementById('aggGallery-prev');
            const nextBtn = document.getElementById('aggGallery-next');
            let currentGallery = null;
            let currentIndex = 0;

            function showLightbox() {
                modal.style.display = 'flex';
                gsap.to(modal, { opacity: 1, duration: 0.3 });
            }

            function hideLightbox() {
                gsap.to(modal, {
                    opacity: 0,
                    duration: 0.3,
                    onComplete: () => {
                        modal.style.display = 'none';
                    }
                });
            }

            function navigateImage(direction) {
                if (!currentGallery) return;
                const images = currentGallery.querySelectorAll('figure.wp-block-image img');
                currentIndex = (currentIndex + direction + images.length) % images.length;
                modalImg.src = images[currentIndex].src;
            }

            // Setup lightbox for galleries
            const lightboxGalleries = document.querySelectorAll('.wp-block-gallery.agg-lightbox');
            lightboxGalleries.forEach(gallery => {
                const images = gallery.querySelectorAll('figure.wp-block-image img');
                images.forEach((img, index) => {
                    img.style.cursor = 'pointer';
                    img.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentGallery = gallery;
                        currentIndex = index;
                        modalImg.src = img.src;
                        showLightbox();
                    });
                });
            });

            // Event listeners
            closeBtn.onclick = hideLightbox;
            modal.onclick = (e) => {
                if (e.target === modal) hideLightbox();
            };
            prevBtn.onclick = (e) => {
                e.stopPropagation();
                navigateImage(-1);
            };
            nextBtn.onclick = (e) => {
                e.stopPropagation();
                navigateImage(1);
            };

            // Keyboard navigation
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
    });
})(jQuery);