<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - IJRPR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .register-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        .input-field {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.2s;
        }
        .input-field:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }
        .input-wrapper {
            position: relative;
        }
        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: #8b5cf6;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background: #7c3aed;
        }
        .error-text {
            color: #dc2626;
            font-size: 12px;
            margin-top: 4px;
        }
        .password-requirements {
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px;
            font-size: 12px;
            color: #4b5563;
            margin-top: 8px;
        }
        .password-requirements ul {
            list-style: disc;
            margin-left: 20px;
            margin-top: 8px;
        }
        .password-requirements li {
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="register-card max-w-md w-full p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Register</h2>
                <p class="mt-2 text-sm text-gray-600">Create your IJRPR account</p>
            </div>
            
            <form method="POST" action="{{ route('register') }}" class="space-y-5" autocomplete="off">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Name<span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="input-field" placeholder="Enter your full name" autocomplete="off">
                    @error('name')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email<span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="email" value="{{ old('email') }}" required
                        class="input-field" placeholder="Enter your email" autocomplete="off"
                        onblur="validateEmail(this)">
                    <div id="email-feedback" class="mt-2 text-sm hidden"></div>
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Password<span class="text-red-500">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input id="password" type="password" name="password" required
                            class="input-field" placeholder="Enter your password"
                            onblur="validatePassword()" autocomplete="new-password">
                        <span class="password-toggle" onclick="togglePassword('password', 'eye-icon-1')">
                            <svg id="eye-icon-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                    </div>
                    <div id="password-feedback" class="mt-2 text-sm hidden"></div>
                    @error('password')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                    <div class="password-requirements">
                        <strong>Password Requirements:</strong>
                        <ul>
                            <li>At least 12 characters long</li>
                            <li>At least one uppercase letter (A-Z)</li>
                            <li>At least one lowercase letter (a-z)</li>
                            <li>At least one number (0-9)</li>
                            <li>At least one special character (!@#$%^&*)</li>
                        </ul>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Confirm Password<span class="text-red-500">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="input-field" placeholder="Confirm your password"
                            onblur="validateConfirmPassword()" autocomplete="new-password">
                        <span class="password-toggle" onclick="togglePassword('password_confirmation', 'eye-icon-2')">
                            <svg id="eye-icon-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px; height: 20px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </span>
                    </div>
                    <div id="confirm-feedback" class="mt-2 text-sm hidden"></div>
                </div>

                <!-- <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_V2_SITE_KEY') }}"></div>
                @error('g-recaptcha-response')
                    <p class="error-text">{{ $message }}</p>
                @enderror -->

                <button type="submit" class="btn-primary">
                    Register
                </button>

                <p class="text-center text-sm text-gray-600">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-700 font-semibold">Login</a>
                </p>
            </form>
        </div>
    </div>

    <!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
    <script>
        function validateEmail(input) {
            const val = input.value.trim();
            const fb  = document.getElementById('email-feedback');
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            fb.classList.remove('hidden');
            if (val === '') {
                fb.innerHTML = '<span style="color:#dc2626;">✘ Email is required</span>';
            } else if (!regex.test(val)) {
                fb.innerHTML = '<span style="color:#dc2626;">✘ Please enter a valid email address (e.g. name@example.com)</span>';
            } else {
                fb.innerHTML = '<span style="color:#16a34a;">✔ Email looks good</span>';
            }
        }

        function validatePassword() {
            const val = document.getElementById('password').value;
            const fb  = document.getElementById('password-feedback');
            const errors = [];

            if (val.length < 12)          errors.push('At least 12 characters');
            if (!/[A-Z]/.test(val))        errors.push('One uppercase letter');
            if (!/[a-z]/.test(val))        errors.push('One lowercase letter');
            if (!/[0-9]/.test(val))        errors.push('One number');
            if (!/[@$!%*#?&]/.test(val))   errors.push('One special character (!@#$%^&*)');

            fb.classList.remove('hidden');
            if (errors.length === 0) {
                fb.innerHTML = '<span style="color:#16a34a;">✔ Password looks good</span>';
            } else {
                fb.innerHTML = '<span style="color:#dc2626;">✘ Missing: ' + errors.join(' &bull; ') + '</span>';
            }
        }

        function validateConfirmPassword() {
            const pw  = document.getElementById('password').value;
            const cpw = document.getElementById('password_confirmation').value;
            const fb  = document.getElementById('confirm-feedback');

            fb.classList.remove('hidden');
            if (cpw === '') { fb.innerHTML = ''; return; }
            if (pw === cpw) {
                fb.innerHTML = '<span style="color:#16a34a;">✔ Passwords match</span>';
            } else {
                fb.innerHTML = '<span style="color:#dc2626;">✘ Passwords do not match</span>';
            }
        }

        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />';
            }
        }
    </script>
</body>
</html>
