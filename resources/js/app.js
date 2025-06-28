import './bootstrap';

// Mobile menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            const isExpanded = mobileMenuToggle.getAttribute('aria-expanded') === 'true';
            
            // Toggle aria-expanded
            mobileMenuToggle.setAttribute('aria-expanded', !isExpanded);
            
            // Toggle menu visibility
            mobileMenu.classList.toggle('hidden');
            
            // Toggle hamburger icon (optional: you can add icon transformation here)
            const icon = mobileMenuToggle.querySelector('svg');
            if (icon) {
                // You can add icon rotation or transformation here if needed
                icon.style.transform = isExpanded ? 'rotate(0deg)' : 'rotate(90deg)';
            }
        });
    }
    
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (mobileMenu && mobileMenuToggle) {
            const isClickInsideMenu = mobileMenu.contains(event.target);
            const isClickOnToggle = mobileMenuToggle.contains(event.target);
            
            if (!isClickInsideMenu && !isClickOnToggle && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
            }
        }
    });
    
    // Form validation enhancements (for article creation/editing)
    const articleForm = document.querySelector('form[action*="articles"]');
    if (articleForm) {
        // Auto-resize textarea
        const textarea = articleForm.querySelector('textarea#content');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }
        
        // Character count for title (optional)
        const titleInput = articleForm.querySelector('input#title');
        if (titleInput) {
            titleInput.addEventListener('input', function() {
                const maxLength = 100; // Recommended title length
                const currentLength = this.value.length;
                
                // You can add character count display here if needed
                if (currentLength > maxLength) {
                    console.warn(`Title is getting long (${currentLength} characters). Consider keeping it under ${maxLength} characters for better SEO.`);
                }
            });
        }
    }
    
    // Dark mode support (if you want to add toggle functionality later)
    // This is a basic setup for future dark mode implementation
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});

// Export functions for potential reuse
window.App = {
    toggleMobileMenu: function() {
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        if (mobileMenuToggle) {
            mobileMenuToggle.click();
        }
    },
    
    // Utility function for form validation
    validateForm: function(formSelector) {
        const form = document.querySelector(formSelector);
        if (!form) return false;
        
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-300', 'focus:ring-red-500', 'focus:border-red-500');
            }
        });
        
        return isValid;
    }
};
