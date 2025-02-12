(function($) {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        const buttonGroups = document.querySelectorAll('.agg-button-group');
        const durationInput = document.querySelector('.agg-duration-input');
        let currentHoverEffect = null;
        let durationTimeout = null;

        function playAnimation() {
            // Get all preview items and settings
            const items = document.querySelectorAll('.agg-preview-item');
            const animationType = document.getElementById('animation_type').value;
            const animationStyle = document.getElementById('animation_style').value;
            const duration = parseFloat(document.querySelector('input[name="agg_settings[animation_duration]"]').value);

            // Kill any existing animations
            items.forEach(item => {
                gsap.killTweensOf(item);
                gsap.set(item, { clearProps: "all" });
            });

            // Reset and animate each item
            items.forEach((item, index) => {
                // Clone the item to clear event listeners
                const clone = item.cloneNode(true);
                item.parentNode.replaceChild(clone, item);

                // Apply new animation
                gsap.set(clone, { opacity: 0 });  // Ensure item starts invisible

                let animationConfig = {
                    duration: duration,
                    ease: "power2.out",
                    onComplete: () => {
                        // Apply hover effect after animation
                        if (currentHoverEffect && currentHoverEffect !== 'none') {
                            AGGAnimations.applyHoverEffect(clone, currentHoverEffect);
                        }
                    }
                };

                if (animationStyle === 'sequence') {
                    animationConfig.delay = index * 0.1;
                }

                switch(animationType) {
                    case 'alternate-scroll':
                        const initialY = index % 2 === 0 ? 100 : -100;
                        gsap.fromTo(clone,
                            { 
                                y: initialY, 
                                opacity: 0,
                                scale: 1
                            },
                            {
                                y: 0,
                                opacity: 1,
                                scale: 1,
                                ...animationConfig
                            }
                        );
                        break;
                        
                    case 'fade':
                        gsap.to(clone, {
                            opacity: 1,
                            ...animationConfig
                        });
                        break;
                        
                    case 'fade-up':
                        gsap.fromTo(clone,
                            { y: 50, opacity: 0 },
                            {
                                y: 0,
                                opacity: 1,
                                ...animationConfig
                            }
                        );
                        break;
                        
                    case 'fade-left':
                        gsap.fromTo(clone,
                            { x: -50, opacity: 0 },
                            {
                                x: 0,
                                opacity: 1,
                                ...animationConfig
                            }
                        );
                        break;
                        
                    case 'zoom':
                        gsap.fromTo(clone,
                            { scale: 0.5, opacity: 0 },
                            {
                                scale: 1,
                                opacity: 1,
                                ease: "back.out(1.7)",
                                ...animationConfig
                            }
                        );
                        break;
                }
            });
        }

        function showSaveReminder() {
            const reminder = document.querySelector('.agg-save-reminder');
            if (reminder) {
                gsap.fromTo(reminder,
                    { opacity: 0, y: 10 },
                    { opacity: 1, y: 0, duration: 0.3 }
                );
            }
        }

        // Button click handlers
        buttonGroups.forEach(group => {
            const buttons = group.querySelectorAll('.agg-button');
            const hiddenInput = group.nextElementSibling;
            
            buttons.forEach(button => {
                button.addEventListener('click', () => {
                    // Update active state
                    group.querySelectorAll('.agg-button').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    button.classList.add('active');
                    hiddenInput.value = button.dataset.value;
                    
                    // Handle different button types
                    if (hiddenInput.id === 'hover_effect') {
                        currentHoverEffect = button.dataset.value;
                        const items = document.querySelectorAll('.agg-preview-item');
                        items.forEach(item => {
                            const clone = item.cloneNode(true);
                            item.parentNode.replaceChild(clone, item);
                            if (currentHoverEffect !== 'none') {
                                AGGAnimations.applyHoverEffect(clone, currentHoverEffect);
                            }
                        });
                    } else if (hiddenInput.id === 'animation_type' || hiddenInput.id === 'animation_style') {
                        setTimeout(playAnimation, 50);
                    }

                    showSaveReminder();
                });
            });
        });

        // Duration input handler
        if (durationInput) {
            durationInput.addEventListener('input', function() {
                if (durationTimeout) {
                    clearTimeout(durationTimeout);
                }

                let value = parseFloat(this.value);
                if (value < 0.1) value = 0.1;
                if (value > 3) value = 3;
                this.value = value;

                durationTimeout = setTimeout(() => {
                    playAnimation();
                    showSaveReminder();
                }, 300);
            });

            durationInput.addEventListener('change', function() {
                let value = parseFloat(this.value);
                if (value < 0.1) this.value = 0.1;
                if (value > 3) this.value = 3;
            });
        }

        // Initialize hover effect
        const initialHoverEffect = document.getElementById('hover_effect').value;
        if (initialHoverEffect !== 'none') {
            currentHoverEffect = initialHoverEffect;
            const items = document.querySelectorAll('.agg-preview-item');
            items.forEach(item => {
                AGGAnimations.applyHoverEffect(item, initialHoverEffect);
            });
        }

        // Play initial animation
        setTimeout(playAnimation, 500);

        // Initialize save reminder
        const reminder = document.querySelector('.agg-save-reminder');
        if (reminder) {
            gsap.set(reminder, { opacity: 0 });
        }
    });
})(jQuery);