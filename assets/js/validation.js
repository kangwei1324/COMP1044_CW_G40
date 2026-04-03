document.addEventListener('DOMContentLoaded', () => {
    const scoreInputs = document.querySelectorAll('.score-input');
    const totalScoreField = document.getElementById('total_score');

    if (!scoreInputs.length || !totalScoreField) return;

    function calculateTotal() {
        let total = 0;
        let isValid = true;

        scoreInputs.forEach(input => {
            const val = parseFloat(input.value) || 0;
            const max = parseFloat(input.getAttribute('max'));
            
            // Client-side visual validation
            if (val > max) {
                input.style.borderColor = 'var(--danger-color)';
                isValid = false;
            } else if (val < 0) {
                input.style.borderColor = 'var(--danger-color)';
                isValid = false;
            } else {
                input.style.borderColor = 'var(--border-color)';
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
