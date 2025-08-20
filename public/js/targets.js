// Targets JavaScript

// Global functions untuk diakses dari HTML
window.removeField = function(button) {
    // Cari container field yang tepat - bisa field yang sudah ada atau yang baru ditambahkan
    const field = button.closest('div.bg-gray-800\\/30');
    
    if (field) {
        field.remove();
    } else {
        // Fallback: cari parent element yang memiliki class yang sesuai
        let current = button.parentElement;
        while (current && current !== document.body) {
            if (current.classList.contains('bg-gray-800') || 
                current.classList.contains('bg-gray-800/30') ||
                current.className.includes('bg-gray-800')) {
                current.remove();
                break;
            }
            current = current.parentElement;
        }
    }
};

window.moveField = function(button, direction) {
    const field = button.closest('div.bg-gray-800\\/30');
    const formFields = document.getElementById('form-fields');
    const actionButtons = document.getElementById('action-buttons');
    
    if (!field) return;
    
    if (direction === 'up') {
        const prevField = field.previousElementSibling;
        if (prevField && prevField.id !== 'action-buttons') {
            formFields.insertBefore(field, prevField);
        }
    } else if (direction === 'down') {
        const nextField = field.nextElementSibling;
        if (nextField && nextField.id !== 'action-buttons') {
            formFields.insertBefore(nextField, field);
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    let fieldCounter = 0;
    let modalRowCounter = 0;
    let currentOptionField = null;
    
    // Tombol Tambah Input
    const addInputBtn = document.getElementById('tambah-input-btn');
    if (addInputBtn) {
        addInputBtn.addEventListener('click', function() {
            addInputField();
        });
    }
    
    // Tombol Tambah Pilihan
    const addOptionBtn = document.getElementById('tambah-pilihan-btn');
    if (addOptionBtn) {
        addOptionBtn.addEventListener('click', function() {
            addOptionField();
        });
    }
    
    // Load existing fields for edit page
    loadExistingFields();
    

    
    // Modal handlers
    const modalPilihan = document.getElementById('modal-pilihan');
    const batalModalBtn = document.getElementById('batal-modal-pilihan');
    const simpanModalBtn = document.getElementById('simpan-modal-pilihan');
    const tambahRowBtn = document.getElementById('tambah-row-pilihan');
    
    // Close modal
    function closeModal() {
        modalPilihan.classList.add('hidden');
        currentOptionField = null;
        // Clear table body
        document.getElementById('table-pilihan-body').innerHTML = '';
        modalRowCounter = 0;
    }
    
    // Open modal
    window.openModal = function(optionField) {
        currentOptionField = optionField;
        
        modalPilihan.classList.remove('hidden');
        
        // Clear existing rows
        document.getElementById('table-pilihan-body').innerHTML = '';
        modalRowCounter = 0;
        
        // Load existing data if available
        const input = optionField.querySelector('input[name*="[pilihan]"]');
        const hiddenInput = optionField.querySelector('input[name*="[pilihan_data]"]');
        
        // Try to load data from hidden input first, then from data-pilihan attribute
        let existingData = [];
        
        if (hiddenInput && hiddenInput.value) {
            try {
                existingData = JSON.parse(hiddenInput.value);
            } catch (e) {
                console.error('Error parsing hidden input data:', e);
            }
        } else if (input && input.getAttribute('data-pilihan')) {
            try {
                existingData = JSON.parse(input.getAttribute('data-pilihan'));
            } catch (e) {
                console.error('Error parsing data-pilihan attribute:', e);
            }
        }
        
        // Load existing data into modal
        if (Array.isArray(existingData) && existingData.length > 0) {
            existingData.forEach(item => {
                addModalRowWithData(item.nilai_provider || '', item.nilai_validasi || '', item.judul || '');
            });
        } else {
            // Add initial row if no existing data
            addModalRow();
        }
    }
    
    // Add row to modal table with existing data
    function addModalRowWithData(nilaiProvider, nilaiValidasi, judul) {
        modalRowCounter++;
        const tableBody = document.getElementById('table-pilihan-body');
        const newRow = document.createElement('div');
        newRow.className = 'flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4';
        newRow.innerHTML = `
            <input type="text" name="modal_nilai_provider[${modalRowCounter}]" value="${nilaiProvider || ''}" placeholder="Nilai Provider" class="w-full sm:w-36 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm">
            <input type="text" name="modal_nilai_validasi[${modalRowCounter}]" value="${nilaiValidasi || ''}" placeholder="Nilai Validasi" class="w-full sm:w-36 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm">
            <input type="text" name="modal_judul[${modalRowCounter}]" value="${judul || ''}" placeholder="Judul" class="w-full sm:w-36 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm">
            <button type="button" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition-colors duration-200 w-full sm:w-auto flex justify-center modal-remove-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;
        tableBody.appendChild(newRow);
        
        // Add event listener to remove button
        const removeBtn = newRow.querySelector('.modal-remove-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                removeModalRow(this);
            });
        }
    }
    
    // Add row to modal table
    function addModalRow() {
        modalRowCounter++;
        const tableBody = document.getElementById('table-pilihan-body');
        const newRow = document.createElement('div');
        newRow.className = 'flex flex-col sm:flex-row items-start sm:items-center gap-2 sm:gap-4';
        newRow.innerHTML = `
            <input type="text" name="modal_nilai_provider[${modalRowCounter}]" placeholder="Nilai Provider" class="w-full sm:w-36 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm">
            <input type="text" name="modal_nilai_validasi[${modalRowCounter}]" placeholder="Nilai Validasi" class="w-full sm:w-36 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm">
            <input type="text" name="modal_judul[${modalRowCounter}]" placeholder="Judul" class="w-full sm:w-36 bg-gray-800/50 border border-gray-600/30 rounded-lg px-3 py-2 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60 text-sm">
            <button type="button" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg transition-colors duration-200 w-full sm:w-auto flex justify-center modal-remove-btn">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;
        tableBody.appendChild(newRow);
        
        // Add event listener to remove button
        const removeBtn = newRow.querySelector('.modal-remove-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                removeModalRow(this);
            });
        }
    }
    
    // Event listeners for modal
    if (batalModalBtn) {
        batalModalBtn.addEventListener('click', closeModal);
    }
    
    if (tambahRowBtn) {
        tambahRowBtn.addEventListener('click', addModalRow);
    }
    
    if (simpanModalBtn) {
        simpanModalBtn.addEventListener('click', function() {
            // Collect data from modal
            const rows = document.querySelectorAll('#table-pilihan-body > div');
            const pilihanData = [];
            
            rows.forEach((row, index) => {
                const inputs = row.querySelectorAll('input');
                // Ubah kondisi: simpan data jika ada minimal satu field yang diisi
                if (inputs[0].value || inputs[1].value || inputs[2].value) {
                    pilihanData.push({
                        nilai_provider: inputs[0].value || '',
                        nilai_validasi: inputs[1].value || '',
                        judul: inputs[2].value || ''
                    });
                }
            });
            
            // Use global function to save pilihan data
            if (pilihanData.length > 0) {
                window.savePilihanData(pilihanData);
            }
            
            closeModal();
        });
    }
    
    // Global function to save pilihan data
    window.savePilihanData = function(pilihanData) {
        
        // If we have currentOptionField, save to that specific field
        if (currentOptionField) {
            const input = currentOptionField.querySelector('input[name*="[pilihan]"]');
            const hiddenInput = currentOptionField.querySelector('input[name*="[pilihan_data]"]');
            
            if (input && hiddenInput) {
                const jsonData = JSON.stringify(pilihanData);
                hiddenInput.value = jsonData;
                input.setAttribute('data-pilihan', jsonData);
            }
        } else {
            // Fallback: Find all option fields and update their hidden inputs
            const optionFields = document.querySelectorAll('input[name*="[pilihan]"]');
            
            optionFields.forEach((input, index) => {
                const hiddenInput = input.parentNode.querySelector('input[name*="[pilihan_data]"]');
                if (hiddenInput) {
                    const jsonData = JSON.stringify(pilihanData);
                    hiddenInput.value = jsonData;
                    input.setAttribute('data-pilihan', jsonData);
                }
            });
        }
    }
    
    // Close modal when clicking outside
    if (modalPilihan) {
        modalPilihan.addEventListener('click', function(e) {
            if (e.target === modalPilihan) {
                closeModal();
            }
        });
    }
    
    function addInputField() {
        fieldCounter++;
        const formFields = document.getElementById('form-fields');
        const actionButtons = document.getElementById('action-buttons');
        
        const newField = document.createElement('div');
        newField.className = 'flex items-center gap-4 bg-gray-800/30 rounded-lg p-4 border border-gray-700/30';
        newField.innerHTML = `
            <div class="flex flex-col gap-1">
                <button type="button" class="text-gray-400 hover:text-white transition-colors duration-200" onclick="moveField(this, 'up')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
                <button type="button" class="text-gray-400 hover:text-white transition-colors duration-200" onclick="moveField(this, 'down')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1">
                <input 
                    type="text" 
                    name="input_fields[${fieldCounter}][judul_kolom]" 
                    placeholder="Judul Kolom"
                    class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                    required
                >
            </div>
            <div class="w-32">
                <select 
                    name="input_fields[${fieldCounter}][validasi]" 
                    class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                >
                    <option value="teks" class="bg-gray-800 text-white">Teks</option>
                    <option value="angka" class="bg-gray-800 text-white">Angka</option>
                    <option value="email" class="bg-gray-800 text-white">Email</option>
                    <option value="password" class="bg-gray-800 text-white">Password</option>
                </select>
            </div>
            <button type="button" class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-lg transition-colors duration-200" onclick="removeField(this)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;
        
        // Masukkan field baru setelah tombol action
        actionButtons.parentNode.insertBefore(newField, actionButtons.nextSibling);
    }
    
    function addOptionField() {
        fieldCounter++;
        const formFields = document.getElementById('form-fields');
        const actionButtons = document.getElementById('action-buttons');
        
        const newField = document.createElement('div');
        newField.className = 'flex items-center gap-4 bg-gray-800/30 rounded-lg p-4 border border-gray-700/30';
        newField.innerHTML = `
            <div class="flex flex-col gap-1">
                <button type="button" class="text-gray-400 hover:text-white transition-colors duration-200" onclick="moveField(this, 'up')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
                <button type="button" class="text-gray-400 hover:text-white transition-colors duration-200" onclick="moveField(this, 'down')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
            </div>
            <div class="w-110">
                <input 
                    type="text" 
                    name="option_fields[${fieldCounter}][pilihan]" 
                    placeholder="Judul Kolom"
                    class="w-full bg-gray-800/50 border border-gray-600/30 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-purple-400/60"
                    required
                >
                <input 
                    type="hidden" 
                    name="option_fields[${fieldCounter}][pilihan_data]" 
                    value=""
                >
            </div>
            <button type="button" class="bg-gradient-to-r from-purple-500 to-purple-400 hover:from-purple-600 hover:to-purple-500 text-white px-4 py-2 rounded-full font-medium transition-all duration-200" onclick="openModal(this.closest('.flex.items-center.gap-4'))">
                Pilihan
            </button>
            <button type="button" class="bg-red-500 hover:bg-red-600 text-white p-3 rounded-lg transition-colors duration-200" onclick="removeField(this)">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        `;
        
        // Masukkan field baru setelah tombol action
        actionButtons.parentNode.insertBefore(newField, actionButtons.nextSibling);
    }
    
    // Load existing fields for edit page
    function loadExistingFields() {
        // Check if we're on edit page by looking for existing data
        const existingInputFields = document.querySelectorAll('input[name*="[judul_kolom]"]');
        const existingOptionFields = document.querySelectorAll('input[name*="[pilihan]"]');
        
        // Update field counter based on existing fields
        if (existingInputFields.length > 0 || existingOptionFields.length > 0) {
            fieldCounter = Math.max(existingInputFields.length, existingOptionFields.length);
        }
    }





    // Fungsi untuk menghapus row di modal
    function removeModalRow(button) {
        const row = button.closest('.flex.flex-col.sm\\:flex-row');
        if (row) {
            row.remove();
        } else {
            // Fallback: cari parent element yang memiliki class yang sesuai
            let current = button.parentElement;
            while (current && current !== document.body) {
                if (current.classList.contains('flex') && 
                    (current.classList.contains('flex-col') || current.className.includes('flex-col'))) {
                    current.remove();
                    break;
                }
                current = current.parentElement;
            }
        }
    }
});
