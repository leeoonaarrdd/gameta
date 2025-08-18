// Member Authentication Form Validation
document.addEventListener('DOMContentLoaded', function() {
    // Login Form Validation
    const loginForm = document.querySelector('form[action*="login"]');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username');
            const password = document.getElementById('password');
            let isValid = true;

            // Clear previous error messages
            clearErrors();

            // Validate username
            if (!username.value.trim()) {
                showError(username, 'Username wajib diisi');
                isValid = false;
            }

            // Validate password
            if (!password.value.trim()) {
                showError(password, 'Password wajib diisi');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // Register Form Validation
    const registerForm = document.querySelector('form[action*="register"]');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            const name = document.getElementById('name');
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const phone = document.getElementById('phone');
            const password = document.getElementById('password');
            const passwordConfirmation = document.getElementById('password_confirmation');
            let isValid = true;

            // Clear previous error messages
            clearErrors();

            // Validate name
            if (!name.value.trim()) {
                showError(name, 'Nama lengkap wajib diisi');
                isValid = false;
            } else if (name.value.trim().length < 2) {
                showError(name, 'Nama minimal 2 karakter');
                isValid = false;
            }

            // Validate username
            if (!username.value.trim()) {
                showError(username, 'Username wajib diisi');
                isValid = false;
            } else if (username.value.trim().length < 3) {
                showError(username, 'Username minimal 3 karakter');
                isValid = false;
            } else if (!/^[a-zA-Z0-9_]+$/.test(username.value.trim())) {
                showError(username, 'Username hanya boleh berisi huruf, angka, dan underscore');
                isValid = false;
            }

            // Validate email
            if (!email.value.trim()) {
                showError(email, 'Email wajib diisi');
                isValid = false;
            } else if (!isValidEmail(email.value.trim())) {
                showError(email, 'Format email tidak valid');
                isValid = false;
            }

            // Validate phone
            if (!phone.value.trim()) {
                showError(phone, 'Nomor telepon wajib diisi');
                isValid = false;
            } else if (!isValidPhone(phone.value.trim())) {
                showError(phone, 'Format nomor telepon tidak valid');
                isValid = false;
            }

            // Validate password
            if (!password.value.trim()) {
                showError(password, 'Password wajib diisi');
                isValid = false;
            } else if (password.value.length < 8) {
                showError(password, 'Password minimal 8 karakter');
                isValid = false;
            }

            // Validate password confirmation
            if (!passwordConfirmation.value.trim()) {
                showError(passwordConfirmation, 'Konfirmasi password wajib diisi');
                isValid = false;
            } else if (password.value !== passwordConfirmation.value) {
                showError(passwordConfirmation, 'Konfirmasi password tidak cocok');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // Helper functions
    function showError(field, message) {
        field.classList.add('border-red-500');
        const errorDiv = document.createElement('p');
        errorDiv.className = 'mt-1 text-sm text-red-400';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    function clearErrors() {
        // Remove error borders
        document.querySelectorAll('.border-red-500').forEach(field => {
            field.classList.remove('border-red-500');
        });

        // Remove error messages
        document.querySelectorAll('.text-red-400').forEach(error => {
            if (error.tagName === 'P' && error.parentNode.querySelector('input')) {
                error.remove();
            }
        });
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function isValidPhone(phone) {
        const phoneRegex = /^(\+62|62|0)8[1-9][0-9]{6,9}$/;
        return phoneRegex.test(phone);
    }

    // Real-time validation for register form
    if (registerForm) {
        const fields = ['name', 'username', 'email', 'phone', 'password', 'password_confirmation'];
        
        fields.forEach(fieldName => {
            const field = document.getElementById(fieldName);
            if (field) {
                field.addEventListener('blur', function() {
                    validateField(field, fieldName);
                });

                field.addEventListener('input', function() {
                    // Clear error when user starts typing
                    if (field.classList.contains('border-red-500')) {
                        field.classList.remove('border-red-500');
                        const errorMessage = field.parentNode.querySelector('.text-red-400');
                        if (errorMessage) {
                            errorMessage.remove();
                        }
                    }
                });
            }
        });
    }

    function validateField(field, fieldName) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';

        switch (fieldName) {
            case 'name':
                if (!value) {
                    message = 'Nama lengkap wajib diisi';
                    isValid = false;
                } else if (value.length < 2) {
                    message = 'Nama minimal 2 karakter';
                    isValid = false;
                }
                break;

            case 'username':
                if (!value) {
                    message = 'Username wajib diisi';
                    isValid = false;
                } else if (value.length < 3) {
                    message = 'Username minimal 3 karakter';
                    isValid = false;
                } else if (!/^[a-zA-Z0-9_]+$/.test(value)) {
                    message = 'Username hanya boleh berisi huruf, angka, dan underscore';
                    isValid = false;
                }
                break;

            case 'email':
                if (!value) {
                    message = 'Email wajib diisi';
                    isValid = false;
                } else if (!isValidEmail(value)) {
                    message = 'Format email tidak valid';
                    isValid = false;
                }
                break;

            case 'phone':
                if (!value) {
                    message = 'Nomor telepon wajib diisi';
                    isValid = false;
                } else if (!isValidPhone(value)) {
                    message = 'Format nomor telepon tidak valid';
                    isValid = false;
                }
                break;

            case 'password':
                if (!value) {
                    message = 'Password wajib diisi';
                    isValid = false;
                } else if (value.length < 8) {
                    message = 'Password minimal 8 karakter';
                    isValid = false;
                }
                break;

            case 'password_confirmation':
                const password = document.getElementById('password');
                if (!value) {
                    message = 'Konfirmasi password wajib diisi';
                    isValid = false;
                } else if (password.value !== value) {
                    message = 'Konfirmasi password tidak cocok';
                    isValid = false;
                }
                break;
        }

        // Clear previous error
        field.classList.remove('border-red-500');
        const existingError = field.parentNode.querySelector('.text-red-400');
        if (existingError) {
            existingError.remove();
        }

        // Show error if invalid
        if (!isValid) {
            field.classList.add('border-red-500');
            const errorDiv = document.createElement('p');
            errorDiv.className = 'mt-1 text-sm text-red-400';
            errorDiv.textContent = message;
            field.parentNode.appendChild(errorDiv);
        }
    }
});
