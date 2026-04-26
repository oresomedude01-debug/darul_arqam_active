/**
 * Darul Arqam School Management System
 * Custom JavaScript for interactivity and UI enhancements
 */

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Initialize application
 */
function initializeApp() {
    setupCSRFToken();
    initializeTooltips();
    initializePopovers();
    initializeFormValidation();
    initializeFileUploads();
    initializeDataTables();
}

/**
 * Setup CSRF Token for AJAX requests
 */
function setupCSRFToken() {
    const token = document.querySelector('meta[name="csrf-token"]');
    if (token) {
        window.csrfToken = token.content;
    }
}

/**
 * Toast Notification System
 */
const Toast = {
    show: function(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 max-w-md p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full ${this.getTypeClass(type)}`;

        const icon = this.getIcon(type);

        toast.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <i class="${icon} text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="flex-shrink-0 ml-4 hover:opacity-70">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);

        // Auto remove
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },

    getTypeClass: function(type) {
        const classes = {
            'success': 'bg-green-50 border border-green-200 text-green-800',
            'error': 'bg-red-50 border border-red-200 text-red-800',
            'warning': 'bg-yellow-50 border border-yellow-200 text-yellow-800',
            'info': 'bg-blue-50 border border-blue-200 text-blue-800'
        };
        return classes[type] || classes['info'];
    },

    getIcon: function(type) {
        const icons = {
            'success': 'fas fa-check-circle',
            'error': 'fas fa-exclamation-circle',
            'warning': 'fas fa-exclamation-triangle',
            'info': 'fas fa-info-circle'
        };
        return icons[type] || icons['info'];
    },

    success: function(message, duration) {
        this.show(message, 'success', duration);
    },

    error: function(message, duration) {
        this.show(message, 'error', duration);
    },

    warning: function(message, duration) {
        this.show(message, 'warning', duration);
    },

    info: function(message, duration) {
        this.show(message, 'info', duration);
    }
};

// Make Toast available globally
window.Toast = Toast;

/**
 * Modal System
 */
const Modal = {
    show: function(title, content, options = {}) {
        const modal = document.createElement('div');
        modal.className = 'modal-backdrop';
        modal.setAttribute('x-data', '{ open: true }');
        modal.setAttribute('x-show', 'open');
        modal.setAttribute('@click.self', 'open = false');

        const showFooter = options.showFooter !== false;
        const confirmText = options.confirmText || 'Confirm';
        const cancelText = options.cancelText || 'Cancel';

        modal.innerHTML = `
            <div class="modal" @click.stop>
                <div class="modal-header">
                    <h3 class="text-lg font-semibold text-gray-900">${title}</h3>
                    <button @click="open = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="modal-body">
                    ${content}
                </div>
                ${showFooter ? `
                <div class="modal-footer">
                    <button @click="open = false" class="btn btn-outline">
                        ${cancelText}
                    </button>
                    <button @click="open = false" class="btn btn-primary" id="modal-confirm">
                        ${confirmText}
                    </button>
                </div>
                ` : ''}
            </div>
        `;

        document.body.appendChild(modal);

        return new Promise((resolve) => {
            const confirmBtn = modal.querySelector('#modal-confirm');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', () => {
                    resolve(true);
                    setTimeout(() => modal.remove(), 300);
                });
            }

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    resolve(false);
                    setTimeout(() => modal.remove(), 300);
                }
            });
        });
    },

    confirm: function(message, title = 'Confirm Action') {
        return this.show(title, `<p class="text-gray-700">${message}</p>`, {
            confirmText: 'Yes, confirm',
            cancelText: 'Cancel'
        });
    },

    alert: function(message, title = 'Alert') {
        return this.show(title, `<p class="text-gray-700">${message}</p>`, {
            showFooter: false
        });
    }
};

// Make Modal available globally
window.Modal = Modal;

/**
 * Confirmation Dialog for Delete Actions
 */
function confirmDelete(itemName, callback) {
    Modal.confirm(
        `Are you sure you want to delete "${itemName}"? This action cannot be undone.`,
        'Confirm Delete'
    ).then((confirmed) => {
        if (confirmed && callback) {
            callback();
        }
    });
}

window.confirmDelete = confirmDelete;

/**
 * Form Validation
 */
function initializeFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });

        // Real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });

    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';

    // Required validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'This field is required';
    }

    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }
    }

    // Number validation
    if (field.type === 'number' && value) {
        const min = field.getAttribute('min');
        const max = field.getAttribute('max');

        if (min && parseFloat(value) < parseFloat(min)) {
            isValid = false;
            errorMessage = `Value must be at least ${min}`;
        }

        if (max && parseFloat(value) > parseFloat(max)) {
            isValid = false;
            errorMessage = `Value must be at most ${max}`;
        }
    }

    // Pattern validation
    if (field.hasAttribute('pattern') && value) {
        const pattern = new RegExp(field.getAttribute('pattern'));
        if (!pattern.test(value)) {
            isValid = false;
            errorMessage = field.getAttribute('data-pattern-error') || 'Invalid format';
        }
    }

    // Display error
    showFieldError(field, isValid, errorMessage);

    return isValid;
}

function showFieldError(field, isValid, errorMessage) {
    const formGroup = field.closest('.form-group');
    if (!formGroup) return;

    let errorElement = formGroup.querySelector('.form-error');

    if (!isValid) {
        field.classList.add('error');

        if (!errorElement) {
            errorElement = document.createElement('p');
            errorElement.className = 'form-error';
            field.parentNode.appendChild(errorElement);
        }

        errorElement.textContent = errorMessage;
    } else {
        field.classList.remove('error');
        if (errorElement) {
            errorElement.remove();
        }
    }
}

/**
 * File Upload Enhancement
 */
function initializeFileUploads() {
    const fileUploads = document.querySelectorAll('.file-upload');

    fileUploads.forEach(upload => {
        const input = upload.querySelector('input[type="file"]');
        if (!input) return;

        // Click to upload
        upload.addEventListener('click', () => input.click());

        // Drag and drop
        upload.addEventListener('dragover', (e) => {
            e.preventDefault();
            upload.classList.add('drag-over');
        });

        upload.addEventListener('dragleave', () => {
            upload.classList.remove('drag-over');
        });

        upload.addEventListener('drop', (e) => {
            e.preventDefault();
            upload.classList.remove('drag-over');

            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                handleFileSelect(input);
            }
        });

        // File selection
        input.addEventListener('change', () => handleFileSelect(input));
    });
}

function handleFileSelect(input) {
    const files = Array.from(input.files);
    const fileList = input.closest('.file-upload').querySelector('.file-list');

    if (fileList) {
        fileList.innerHTML = '';
        files.forEach(file => {
            const item = document.createElement('div');
            item.className = 'flex items-center justify-between p-2 bg-gray-50 rounded mt-2';
            item.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas fa-file text-gray-400"></i>
                    <span class="text-sm text-gray-700">${file.name}</span>
                    <span class="text-xs text-gray-500">(${formatFileSize(file.size)})</span>
                </div>
                <button type="button" class="text-red-600 hover:text-red-800">
                    <i class="fas fa-times"></i>
                </button>
            `;
            fileList.appendChild(item);
        });
    }
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

/**
 * Initialize Tooltips
 */
function initializeTooltips() {
    const elements = document.querySelectorAll('[data-tooltip]');

    elements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const text = e.target.getAttribute('data-tooltip');
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = text;
    tooltip.id = 'active-tooltip';

    document.body.appendChild(tooltip);

    const rect = e.target.getBoundingClientRect();
    tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
    tooltip.style.left = (rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2)) + 'px';
}

function hideTooltip() {
    const tooltip = document.getElementById('active-tooltip');
    if (tooltip) {
        tooltip.remove();
    }
}

/**
 * Initialize Popovers
 */
function initializePopovers() {
    // Implement popover logic here if needed
}

/**
 * Initialize Data Tables
 */
function initializeDataTables() {
    // Add sorting, filtering, pagination to tables
    const tables = document.querySelectorAll('.data-table');

    tables.forEach(table => {
        // Add sorting capability
        const headers = table.querySelectorAll('th[data-sortable]');
        headers.forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => sortTable(table, header));
        });
    });
}

function sortTable(table, header) {
    // Basic table sorting implementation
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    const columnIndex = Array.from(header.parentElement.children).indexOf(header);
    const isAscending = header.classList.contains('sort-asc');

    rows.sort((a, b) => {
        const aValue = a.children[columnIndex].textContent.trim();
        const bValue = b.children[columnIndex].textContent.trim();

        return isAscending
            ? bValue.localeCompare(aValue, undefined, { numeric: true })
            : aValue.localeCompare(bValue, undefined, { numeric: true });
    });

    // Update sort indicators
    table.querySelectorAll('th').forEach(th => {
        th.classList.remove('sort-asc', 'sort-desc');
    });

    header.classList.add(isAscending ? 'sort-desc' : 'sort-asc');

    // Re-append sorted rows
    rows.forEach(row => tbody.appendChild(row));
}

/**
 * Utility Functions
 */

// Format date
function formatDate(date, format = 'YYYY-MM-DD') {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');

    return format
        .replace('YYYY', year)
        .replace('MM', month)
        .replace('DD', day);
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export utilities
window.formatDate = formatDate;
window.debounce = debounce;
window.formatFileSize = formatFileSize;

/**
 * Loading Spinner
 */
const Loading = {
    show: function(element = document.body) {
        const spinner = document.createElement('div');
        spinner.id = 'app-loading';
        spinner.className = 'fixed inset-0 bg-white/80 flex items-center justify-center z-50';
        spinner.innerHTML = `
            <div class="text-center">
                <div class="spinner spinner-lg border-4 border-gray-200 border-t-primary-600 mx-auto"></div>
                <p class="mt-4 text-gray-600">Loading...</p>
            </div>
        `;
        element.appendChild(spinner);
    },

    hide: function() {
        const spinner = document.getElementById('app-loading');
        if (spinner) {
            spinner.remove();
        }
    }
};

window.Loading = Loading;

console.log('Darul Arqam School Management System initialized successfully!');
