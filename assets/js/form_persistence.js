/**
 * Form Persistence Script
 * Automatically saves and restores form data using localStorage.
 * To use: Add 'data-persistence-prefix="YOUR_PREFIX_"' to any <form> element.
 */
document.addEventListener('DOMContentLoaded', function() {
    const persistentForms = document.querySelectorAll('form[data-persistence-prefix]');

    persistentForms.forEach(form => {
        const prefix = form.getAttribute('data-persistence-prefix');
        const fields = form.querySelectorAll('input:not([type="password"]):not([type="hidden"]), textarea, select');

        // 1. Restore saved values
        fields.forEach(field => {
            const savedValue = localStorage.getItem(prefix + field.name);
            if (savedValue !== null) {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    field.checked = (savedValue === 'true');
                } else {
                    field.value = savedValue;
                }
                
                // Trigger an input event so other scripts (like calculators) know the value changed
                field.dispatchEvent(new Event('input', { bubbles: true }));
            }

            // 2. Save changes as the user types/interacts
            field.addEventListener('input', () => {
                const valueToSave = (field.type === 'checkbox' || field.type === 'radio') 
                    ? field.checked 
                    : field.value;
                localStorage.setItem(prefix + field.name, valueToSave);
            });
        });

        // 3. Cleanup: Wipe memory on successful form submission
        form.addEventListener('submit', function() {
            // We only wipe if the form is valid (if validation script is present)
            if (!form.checkValidity || form.checkValidity()) {
                fields.forEach(field => {
                    localStorage.removeItem(prefix + field.name);
                });
            }
        });
    });
});
