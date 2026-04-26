document.addEventListener('DOMContentLoaded', function () {
    // Select all forms that have the 'novalidate' attribute
    const forms = document.querySelectorAll('form[novalidate]');

    forms.forEach(form => {
        form.addEventListener('submit', function (event) {
            let isValid = true;
            
            // Re-validate all elements on submit
            const elements = form.querySelectorAll('input, select, textarea');
            elements.forEach(element => {
                if (!validateElement(element)) {
                    isValid = false;
                }
            });

            if (!isValid) {
                event.preventDefault(); // Stop submission if there are errors
            }
        });

        // Add hybrid real-time validation
        const elements = form.querySelectorAll('input, select, textarea');
        elements.forEach(element => {
            // 1. Immediate validation for specific cases as user types
            element.addEventListener('input', function () {
                // If the element has a range error (min/max), show it immediately
                if (element.validity.rangeOverflow || element.validity.rangeUnderflow) {
                    validateElement(element);
                } 
                // If it was already invalid, re-validate to clear/update error as they fix it
                else if (element.classList.contains('is-invalid')) {
                    validateElement(element);
                }
            });

            // 2. Full validation when the user leaves the field
            element.addEventListener('blur', function () {
                validateElement(element);
            });

            // 3. Change event for selects and checkboxes
            element.addEventListener('change', function () {
                validateElement(element);
            });
        });
    });

    function validateElement(element) {
        // Skip elements that shouldn't be validated like hidden inputs or buttons
        if (element.type === 'hidden' || element.type === 'submit' || element.type === 'button') {
            return true;
        }

        clearError(element); // Reset before checking

        if (!element.checkValidity()) {
            showError(element, getErrorMessage(element));
            return false;
        }
        
        return true;
    }

    function getErrorMessage(element) {
        if (element.validity.valueMissing) {
            return 'This field is required.';
        }
        if (element.validity.patternMismatch) {
            return element.title || 'Please match the requested format.';
        }
        if (element.validity.typeMismatch) {
            if (element.type === 'email') return 'Please enter a valid email address.';
            return 'Invalid input type.';
        }
        if (element.validity.rangeOverflow) {
            return `Value must be less than or equal to ${element.getAttribute('max')}.`;
        }
        if (element.validity.rangeUnderflow) {
            return `Value must be greater than or equal to ${element.getAttribute('min')}.`;
        }
        if (element.validity.tooShort) {
            return `Please lengthen this text to ${element.getAttribute('minlength')} characters or more.`;
        }
        return element.validationMessage || 'Invalid value.';
    }

    function showError(element, message) {
        element.classList.add('is-invalid');
        element.style.borderColor = 'var(--danger-color)';

        let feedback = element.nextElementSibling;
        if (!feedback || !feedback.classList.contains('invalid-feedback')) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.style.color = 'var(--danger-color)';
            feedback.style.fontSize = '0.875rem';
            feedback.style.marginTop = '0.25rem';
            
            if (element.type === 'checkbox') {
                element.parentNode.appendChild(feedback);
            } else {
                element.parentNode.insertBefore(feedback, element.nextSibling);
            }
        }
        feedback.textContent = message;
    }

    function clearError(element) {
        element.classList.remove('is-invalid');
        element.style.borderColor = ''; // reset to CSS default
        
        const feedback = element.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.remove();
        }
    }
});
