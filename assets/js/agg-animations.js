/**
 * Shared animations module for Animated Gutenberg Gallery
 */
const AGGAnimations = {
    // Hover Effects
    hoverEffects: {
        zoom: function(item) {
            item.addEventListener('mouseenter', () => {
                gsap.to(item, { 
                    scale: 1.05, 
                    duration: 0.3,
                    ease: "power2.out" 
                });
            });
            item.addEventListener('mouseleave', () => {
                gsap.to(item, { 
                    scale: 1, 
                    duration: 0.3,
                    ease: "power2.out" 
                });
            });
        },

        lift: function(item) {
            item.addEventListener('mouseenter', () => {
                gsap.to(item, { 
                    y: -15, 
                    duration: 0.3,
                    ease: "power2.out" 
                });
            });
            item.addEventListener('mouseleave', () => {
                gsap.to(item, { 
                    y: 0, 
                    duration: 0.3,
                    ease: "power2.out" 
                });
            });
        },

        tilt: function(item) {
            item.addEventListener('mousemove', (e) => {
                const bounds = item.getBoundingClientRect();
                const mouseX = e.clientX - bounds.left;
                const mouseY = e.clientY - bounds.top;
                const centerX = bounds.width / 2;
                const centerY = bounds.height / 2;
                const rotateX = (mouseY - centerY) / 10;
                const rotateY = (centerX - mouseX) / 10;

                gsap.to(item, {
                    rotateX: rotateX,
                    rotateY: rotateY,
                    scale: 1.05,
                    transformPerspective: 300,
                    duration: 0.5,
                    ease: "power2.out"
                });
            });
            item.addEventListener('mouseleave', () => {
                gsap.to(item, {
                    rotateX: 0,
                    rotateY: 0,
                    scale: 1,
                    duration: 0.5,
                    ease: "power2.out"
                });
            });
        }
    },

    applyHoverEffect: function(item, effect) {
        if (this.hoverEffects[effect]) {
            this.hoverEffects[effect](item);
        }
    },

    resetItem: function(item) {
        gsap.killTweensOf(item);
        gsap.set(item, { 
            clearProps: "all",
            transformPerspective: "none",
            rotateX: 0,
            rotateY: 0,
            scale: 1,
            x: 0,
            y: 0,
            opacity: 1
        });
    },

    // Animation Types
    applyAnimation: function(item, options) {
        const {
            type, 
            style = 'group',
            duration = 1,
            index = 0,
            hoverEffect = null,
            isAdmin = false,
        } = options;

        // First reset the item
        this.resetItem(item);

        let config = {
            duration: duration,
            ease: "power2.out",
            onComplete: () => {
                if (hoverEffect && hoverEffect !== 'none') {
                    this.applyHoverEffect(item, hoverEffect);
                }
            }
        };

        // Add delay if sequence style
        if (style === 'sequence') {
            config.delay = index * 0.1;
        }

        // Add scroll trigger for frontend
        if (!isAdmin) {
            config.scrollTrigger = {
                trigger: item,
                start: "top bottom-=100",
                toggleActions: "play none none reverse"
            };
        }

        switch(type) {
            case 'alternate-scroll':
                const initialY = index % 2 === 0 ? 100 : -100;
                gsap.fromTo(item,
                    { 
                        y: initialY, 
                        opacity: 0,
                        scale: 1
                    },
                    {
                        ...config,
                        y: 0,
                        opacity: 1,
                        scale: 1
                    }
                );
                break;
                
            case 'fade':
                gsap.fromTo(item,
                    { opacity: 0 },
                    {
                        ...config,
                        opacity: 1
                    }
                );
                break;
                
            case 'fade-up':
                gsap.fromTo(item,
                    { 
                        opacity: 0,
                        y: 50
                    },
                    {
                        ...config,
                        opacity: 1,
                        y: 0
                    }
                );
                break;
                
            case 'fade-left':
                gsap.fromTo(item,
                    { 
                        opacity: 0,
                        x: -50
                    },
                    {
                        ...config,
                        opacity: 1,
                        x: 0
                    }
                );
                break;
                
            case 'zoom':
                gsap.fromTo(item,
                    { 
                        opacity: 0,
                        scale: 0.5
                    },
                    {
                        ...config,
                        opacity: 1,
                        scale: 1,
                        ease: "back.out(1.7)"
                    }
                );
                break;
        }
    }
};

// Make it available globally
window.AGGAnimations = AGGAnimations;