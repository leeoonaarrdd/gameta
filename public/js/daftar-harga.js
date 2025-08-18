document.addEventListener('DOMContentLoaded', function() {
    // Entries per page functionality
    const entriesSelect = document.querySelector('select[name="entries"]');
    if (entriesSelect) {
        entriesSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
