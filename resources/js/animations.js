/**
 * Enhanced UX Animations using GSAP
 * Provides smooth, professional animations for forms, cards, and interactive elements
 */

// Register GSAP plugins
gsap.registerPlugin(ScrollTrigger);

// ========================================
// FORM ENHANCEMENTS
// ========================================

/**
 * Enhance form inputs with smooth focus animations
 */
function enhanceFormInputs() {
    const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], input[type="date"], input[type="tel"], textarea, select');
    
    inputs.forEach(input => {
        // Add focus animation
        input.addEventListener('focus', function() {
            gsap.to(this, {
                duration: 0.3,
                boxShadow: '0 10px 25px -5px rgba(2, 132, 199, 0.2)',
                ease: 'power2.out'
            });
        });

        // Add blur animation
        input.addEventListener('blur', function() {
            gsap.to(this, {
                duration: 0.3,
                boxShadow: 'none',
                ease: 'power2.out'
            });
        });

        // Add change animation
        input.addEventListener('change', function() {
            gsap.to(this, {
                duration: 0.2,
                scale: 1.02,
                ease: 'back.out',
                onComplete: () => {
                    gsap.to(this, { duration: 0.2, scale: 1 });
                }
            });
        });
    });
}

/**
 * Animate form validation errors and success messages
 */
function enhanceFormValidation() {
    const errorMessages = document.querySelectorAll('.text-red-500');
    const successMessages = document.querySelectorAll('.text-green-500, .success-feedback');

    errorMessages.forEach((msg, index) => {
        gsap.from(msg, {
            duration: 0.4,
            opacity: 0,
            y: -10,
            ease: 'back.out',
            delay: index * 0.05
        });
    });

    successMessages.forEach((msg, index) => {
        gsap.from(msg, {
            duration: 0.4,
            opacity: 0,
            x: 20,
            ease: 'back.out',
            delay: index * 0.05
        });
    });
}

/**
 * Create staggered animation for form groups
 */
function animateFormGroups() {
    const formGroups = document.querySelectorAll('.form-group');
    
    gsap.to(formGroups, {
        duration: 0.6,
        opacity: 1,
        y: 0,
        stagger: 0.08,
        ease: 'power3.out',
        onStart: () => {
            formGroups.forEach(group => {
                group.style.opacity = '0';
                group.style.transform = 'translateY(20px)';
            });
        }
    });
}

// ========================================
// CARD ANIMATIONS
// ========================================

/**
 * Animate card elements with stagger effect
 */
function animateCards() {
    const cards = document.querySelectorAll('.card');
    
    cards.forEach((card, index) => {
        gsap.from(card, {
            duration: 0.7,
            opacity: 0,
            y: 40,
            ease: 'cubic-bezier(0.34, 1.56, 0.64, 1)',
            delay: index * 0.12
        });

        // Add hover animation
        card.addEventListener('mouseenter', function() {
            gsap.to(this, {
                duration: 0.3,
                y: -8,
                boxShadow: '0 20px 40px -10px rgba(0, 0, 0, 0.1)',
                ease: 'power2.out'
            });
        });

        card.addEventListener('mouseleave', function() {
            gsap.to(this, {
                duration: 0.3,
                y: 0,
                boxShadow: '0 1px 3px rgba(0, 0, 0, 0.1)',
                ease: 'power2.out'
            });
        });
    });
}

// ========================================
// LIST ANIMATIONS
// ========================================

/**
 * Animate list items with stagger
 */
function animateListItems() {
    const listItems = document.querySelectorAll('.list-item, tr[role="row"]');
    
    gsap.to(listItems, {
        duration: 0.5,
        opacity: 1,
        x: 0,
        stagger: 0.05,
        ease: 'power2.out',
        onStart: () => {
            listItems.forEach(item => {
                item.style.opacity = '0';
                item.style.transform = 'translateX(-20px)';
            });
        }
    });

    // Add hover effect to list items
    listItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            gsap.to(this, {
                duration: 0.2,
                x: 5,
                ease: 'power2.out'
            });
        });

        item.addEventListener('mouseleave', function() {
            gsap.to(this, {
                duration: 0.2,
                x: 0,
                ease: 'power2.out'
            });
        });
    });
}

// ========================================
// BUTTON ANIMATIONS
// ========================================

/**
 * Enhance button interactions
 */
function enhanceButtons() {
    const buttons = document.querySelectorAll('button, a.btn');
    
    buttons.forEach(btn => {
        btn.addEventListener('mouseenter', function() {
            gsap.to(this, {
                duration: 0.3,
                y: -3,
                boxShadow: '0 10px 25px -5px rgba(2, 132, 199, 0.3)',
                ease: 'power2.out'
            });
        });

        btn.addEventListener('mouseleave', function() {
            gsap.to(this, {
                duration: 0.3,
                y: 0,
                boxShadow: 'none',
                ease: 'power2.out'
            });
        });

        btn.addEventListener('click', function(e) {
            // Create ripple effect
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            ripple.style.width = '0';
            ripple.style.height = '0';
            ripple.style.borderRadius = '50%';
            ripple.style.background = 'rgba(255, 255, 255, 0.5)';
            ripple.style.position = 'absolute';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.style.pointerEvents = 'none';
            ripple.style.transform = 'translate(-50%, -50%)';

            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);

            gsap.to(ripple, {
                duration: 0.6,
                width: '300px',
                height: '300px',
                opacity: 0,
                ease: 'power2.out',
                onComplete: () => ripple.remove()
            });
        });
    });
}

// ========================================
// MODAL ANIMATIONS
// ========================================

/**
 * Animate modals with smooth transitions
 */
function enhanceModals() {
    const modalBackdrops = document.querySelectorAll('[role="dialog"] ~ .fixed');
    
    modalBackdrops.forEach(backdrop => {
        gsap.from(backdrop, {
            duration: 0.3,
            opacity: 0,
            ease: 'power2.out'
        });
    });

    const modals = document.querySelectorAll('[role="dialog"]');
    
    modals.forEach(modal => {
        gsap.from(modal, {
            duration: 0.4,
            opacity: 0,
            scale: 0.95,
            y: -30,
            ease: 'back.out(1.7)'
        });
    });
}

// ========================================
// SCROLL ANIMATIONS
// ========================================

/**
 * Animate elements on scroll using ScrollTrigger
 */
function setupScrollAnimations() {
    // Animate cards when they come into view
    gsap.utils.toArray('.card').forEach((card) => {
        gsap.from(card, {
            scrollTrigger: {
                trigger: card,
                start: 'top bottom-=100px',
                toggleActions: 'play none none none'
            },
            duration: 0.6,
            opacity: 0,
            y: 30,
            ease: 'power2.out'
        });
    });

    // Animate tables when they come into view
    gsap.utils.toArray('table tbody tr').forEach((row, index) => {
        gsap.from(row, {
            scrollTrigger: {
                trigger: row,
                start: 'top bottom',
                toggleActions: 'play none none none'
            },
            duration: 0.5,
            opacity: 0,
            x: -20,
            ease: 'power2.out',
            delay: index * 0.03
        });
    });

    // Refresh ScrollTrigger after page load
    ScrollTrigger.refresh();
}

// ========================================
// SIDEBAR ANIMATIONS
// ========================================

/**
 * Smooth sidebar collapse/expand animation
 */
function enhanceSidebarAnimation() {
    const sidebar = document.querySelector('aside');
    if (!sidebar) return;

    // Observe sidebar width changes
    const observer = new MutationObserver(() => {
        gsap.to(sidebar, {
            duration: 0.3,
            ease: 'power2.inOut'
        });
    });

    observer.observe(sidebar, {
        attributes: true,
        attributeFilter: ['class']
    });
}

// ========================================
// PAGE TRANSITIONS
// ========================================

/**
 * Animate page content transitions
 */
function animatePageContent() {
    const content = document.querySelector('main') || document.querySelector('[role="main"]');
    
    if (content) {
        gsap.from(content, {
            duration: 0.5,
            opacity: 0,
            y: 20,
            ease: 'power2.out'
        });
    }
}

// ========================================
// NUMBER COUNTER ANIMATIONS
// ========================================

/**
 * Animate number counters for statistics
 */
function animateCounters() {
    const counters = document.querySelectorAll('[data-count], .counter');
    
    counters.forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-count') || counter.textContent);
        const duration = 2;

        ScrollTrigger.create({
            trigger: counter,
            onEnter: () => {
                gsap.to(counter, {
                    duration: duration,
                    textContent: target,
                    ease: 'power2.out',
                    snap: { textContent: 1 }
                });
            }
        });
    });
}

// ========================================
// LOADING STATES
// ========================================

/**
 * Show loading animation
 */
window.showLoading = function(message = 'Loading...') {
    const loader = document.createElement('div');
    loader.id = 'global-loader';
    loader.className = 'fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-[9999]';
    loader.innerHTML = `
        <div class="bg-white rounded-lg p-8 text-center shadow-2xl">
            <div class="w-12 h-12 border-4 border-gray-200 border-t-blue-600 rounded-full mx-auto mb-4 animate-spin"></div>
            <p class="text-gray-700 font-medium">${message}</p>
        </div>
    `;
    
    document.body.appendChild(loader);
    
    gsap.from(loader, {
        duration: 0.3,
        opacity: 0,
        ease: 'power2.out'
    });
    
    return loader;
};

/**
 * Hide loading animation
 */
window.hideLoading = function() {
    const loader = document.getElementById('global-loader');
    if (loader) {
        gsap.to(loader, {
            duration: 0.3,
            opacity: 0,
            ease: 'power2.in',
            onComplete: () => loader.remove()
        });
    }
};

// ========================================
// TOAST NOTIFICATIONS
// ========================================

/**
 * Show toast notification
 */
window.showToast = function(message, type = 'info', duration = 3000) {
    const toast = document.createElement('div');
    const bgColor = {
        'success': 'bg-green-500',
        'error': 'bg-red-500',
        'warning': 'bg-yellow-500',
        'info': 'bg-blue-500'
    }[type] || 'bg-blue-500';

    toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg fixed bottom-4 right-4 z-[9999]`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    gsap.from(toast, {
        duration: 0.4,
        y: 100,
        opacity: 0,
        ease: 'back.out(1.7)'
    });
    
    gsap.to(toast, {
        duration: 0.4,
        y: -100,
        opacity: 0,
        ease: 'back.in(1.7)',
        delay: duration / 1000,
        onComplete: () => toast.remove()
    });
};

// ========================================
// INITIALIZATION
// ========================================

/**
 * Initialize all animations when DOM is ready
 */
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure all elements are rendered
    setTimeout(() => {
        enhanceFormInputs();
        enhanceFormValidation();
        animateFormGroups();
        animateCards();
        animateListItems();
        enhanceButtons();
        enhanceModals();
        setupScrollAnimations();
        enhanceSidebarAnimation();
        animatePageContent();
        animateCounters();
    }, 100);
});

/**
 * Reinitialize animations when Alpine.js updates the DOM
 */
document.addEventListener('alpine:init', function() {
    // Wait for Alpine to fully initialize
    setTimeout(() => {
        enhanceFormInputs();
        animateCards();
        animateListItems();
        setupScrollAnimations();
        ScrollTrigger.refresh();
    }, 100);
});

/**
 * Reinitialize animations for dynamic content loaded via AJAX
 */
function reinitializeAnimations() {
    enhanceFormInputs();
    enhanceFormValidation();
    animateFormGroups();
    animateCards();
    animateListItems();
    enhanceButtons();
    setupScrollAnimations();
    ScrollTrigger.refresh();
}

// Export for external use
window.reinitializeAnimations = reinitializeAnimations;
