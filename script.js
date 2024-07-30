document.addEventListener('DOMContentLoaded', function () {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    const greenFill = document.querySelector('.green-fill');
    const percentageParagraph = document.getElementById('percentage');

    function updatePercentage() {
        let totalPercentage = 0;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                totalPercentage += 25;
            }
        });

        percentageParagraph.textContent = `LOADING ${totalPercentage}%...`;
        greenFill.style.width = `${totalPercentage}%`;

        // Save the checkbox states to localStorage
        checkboxes.forEach(checkbox => {
            localStorage.setItem(checkbox.id, checkbox.checked);
        });
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updatePercentage);

        // Restore checkbox states from localStorage
        const savedState = localStorage.getItem(checkbox.id);
        if (savedState === 'true') {
            checkbox.checked = true;
        }
    });

    // Initial percentage update
    updatePercentage();

    
});
