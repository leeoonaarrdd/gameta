document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabAkun = document.getElementById('tab-akun');
    const tabPembelian = document.getElementById('tab-pembelian');
    const questionsAkun = document.getElementById('questions-akun');
    const questionsPembelian = document.getElementById('questions-pembelian');
    
    tabAkun.addEventListener('click', function() {
        // Update tab styles
        tabAkun.classList.add('bg-purple-500', 'text-white');
        tabAkun.classList.remove('text-gray-300');
        tabPembelian.classList.remove('bg-purple-500', 'text-white');
        tabPembelian.classList.add('text-gray-300');
        
        // Show/hide content
        questionsAkun.classList.remove('hidden');
        questionsPembelian.classList.add('hidden');
    });
    
    tabPembelian.addEventListener('click', function() {
        // Update tab styles
        tabPembelian.classList.add('bg-purple-500', 'text-white');
        tabPembelian.classList.remove('text-gray-300');
        tabAkun.classList.remove('bg-purple-500', 'text-white');
        tabAkun.classList.add('text-gray-300');
        
        // Show/hide content
        questionsPembelian.classList.remove('hidden');
        questionsAkun.classList.add('hidden');
    });
});

// Toggle answer visibility
function toggleAnswer(answerId) {
    const answer = document.getElementById(answerId);
    const questionDiv = answer.parentElement;
    const chevron = questionDiv.querySelector('svg');
    
    if (answer.classList.contains('hidden')) {
        answer.classList.remove('hidden');
        chevron.style.transform = 'rotate(90deg)';
    } else {
        answer.classList.add('hidden');
        chevron.style.transform = 'rotate(0deg)';
    }
}