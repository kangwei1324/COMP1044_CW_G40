document.addEventListener('DOMContentLoaded', () => {
    const scoreInputs = document.querySelectorAll('.score-input');
    const totalScoreField = document.getElementById('total_score');

    if (!scoreInputs.length || !totalScoreField) return;

    function calculateTotal() {
        let total = 0;
        let isValid = true;

        scoreInputs.forEach(input => {
            const val = parseFloat(input.value) || 0;
            
            // Note: form_validation.js handles the visual error messages and borders
            // This script only handles the calculation logic for the total score.
            
            // We only want to flag isValid = false if the input is NOT empty 
            // AND fails checkValidity (like going over the max score).
            // If it's empty, we just let it add 0 to the total.
            if (input.value && !input.checkValidity()) {
                isValid = false;
            } else {
                total += val;
            }
        });

        if (isValid) {
            // Cap at 100 just in case, though max properties prevent this
            totalScoreField.value = Math.min(total, 100).toFixed(1) + '%';
            totalScoreField.style.color = 'var(--primary-color)';
        } else {
            totalScoreField.value = 'Invalid Input';
            totalScoreField.style.color = 'var(--danger-color)';
        }
    }

    // Attach event listeners to all scoring inputs
    scoreInputs.forEach(input => {
        input.addEventListener('input', calculateTotal);
    });
});
