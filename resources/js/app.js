// Affilook - Main JavaScript

// Import Alpine.js for reactivity
import Alpine from 'alpinejs'

// Make Alpine available globally
window.Alpine = Alpine

// Start Alpine
Alpine.start()

// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Affilook app loaded successfully!')

    // Initialize tooltips
    initTooltips()

    // Initialize modals
    initModals()

    // Initialize form validation
    initFormValidation()

    // Initialize product interactions
    initProductInteractions()
})

// Tooltips
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]')

    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip)
        element.addEventListener('mouseleave', hideTooltip)
    })
}

function showTooltip(event) {
    const tooltip = document.createElement('div')
    tooltip.className = 'absolute z-50 px-2 py-1 text-sm text-white bg-gray-900 rounded shadow-lg'
    tooltip.textContent = event.target.dataset.tooltip
    tooltip.style.top = event.target.offsetTop - 30 + 'px'
    tooltip.style.left = event.target.offsetLeft + 'px'

    document.body.appendChild(tooltip)
    event.target.tooltipElement = tooltip
}

function hideTooltip(event) {
    if (event.target.tooltipElement) {
        event.target.tooltipElement.remove()
        event.target.tooltipElement = null
    }
}

// Modals
function initModals() {
    const modalTriggers = document.querySelectorAll('[data-modal-target]')
    const modalCloses = document.querySelectorAll('[data-modal-close]')

    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', openModal)
    })

    modalCloses.forEach(close => {
        close.addEventListener('click', closeModal)
    })

    // Close modal on backdrop click
    document.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-backdrop')) {
            closeModal(event)
        }
    })
}

function openModal(event) {
    const modalId = event.target.dataset.modalTarget
    const modal = document.getElementById(modalId)

    if (modal) {
        modal.classList.remove('hidden')
        modal.classList.add('flex')
        document.body.classList.add('overflow-hidden')
    }
}

function closeModal(event) {
    const modal = event.target.closest('.modal')
    if (modal) {
        modal.classList.add('hidden')
        modal.classList.remove('flex')
        document.body.classList.remove('overflow-hidden')
    }
}

// Form Validation
function initFormValidation() {
    const forms = document.querySelectorAll('form[data-validate]')

    forms.forEach(form => {
        form.addEventListener('submit', validateForm)
    })
}

function validateForm(event) {
    const form = event.target
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]')
    let isValid = true

    inputs.forEach(input => {
        if (!input.value.trim()) {
            showFieldError(input, 'Ce champ est requis')
            isValid = false
        } else {
            clearFieldError(input)
        }
    })

    if (!isValid) {
        event.preventDefault()
    }
}

function showFieldError(field, message) {
    clearFieldError(field)

    const error = document.createElement('div')
    error.className = 'text-red-500 text-sm mt-1'
    error.textContent = message

    field.parentNode.appendChild(error)
    field.classList.add('border-red-500')
}

function clearFieldError(field) {
    const error = field.parentNode.querySelector('.text-red-500')
    if (error) {
        error.remove()
    }
    field.classList.remove('border-red-500')
}

// Product Interactions
function initProductInteractions() {
    // Product image gallery
    const productImages = document.querySelectorAll('.product-image-gallery img')

    productImages.forEach(img => {
        img.addEventListener('click', function() {
            const modal = document.getElementById('imageModal')
            const modalImg = modal.querySelector('img')

            if (modal && modalImg) {
                modalImg.src = this.src
                modal.classList.remove('hidden')
                modal.classList.add('flex')
            }
        })
    })

    // Stock color selection
    const colorSelects = document.querySelectorAll('[data-color-select]')

    colorSelects.forEach(select => {
        select.addEventListener('change', function() {
            const productId = this.dataset.productId
            const color = this.value
            updateStockDisplay(productId, color)
        })
    })
}

function updateStockDisplay(productId, color) {
    // This would typically make an AJAX call to get stock for the selected color
    console.log(`Updating stock display for product ${productId}, color ${color}`)
}

// Utility Functions
function showNotification(message, type = 'info') {
    const notification = document.createElement('div')
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        type === 'warning' ? 'bg-yellow-500' :
        'bg-blue-500'
    }`
    notification.textContent = message

    document.body.appendChild(notification)

    setTimeout(() => {
        notification.remove()
    }, 5000)
}

// Export for global use
window.Affilook = {
    showNotification,
    openModal,
    closeModal
}
