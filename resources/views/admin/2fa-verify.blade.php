<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin 2FA Verification - IJRPR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }
        .input-field {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 24px;
            text-align: center;
            letter-spacing: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .input-field:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: #f59e0b;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background: #d97706;
        }
        .error-box {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
        }
        .info-box {
            background: #dbeafe;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            padding: 12px;
            border-radius: 8px;
            font-size: 13px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="login-card max-w-md w-full p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">
                    Admin Verification
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Enter the 6-digit code sent to your email
                </p>
                <p class="mt-1 text-xs text-gray-500">
                    {{ session('2fa_email') }}
                </p>
            </div>

            <form action="{{ route('admin.2fa.verify.code') }}" method="POST" class="space-y-5">
                @csrf

                @if($errors->any())
                    <div class="error-box">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700 mb-2 text-center">
                        Verification Code<span class="text-red-500">*</span>
                    </label>
                    <input id="code" name="code" type="text" required maxlength="6" pattern="[0-9]{6}"
                        class="input-field"
                        placeholder="000000" 
                        autofocus
                        autocomplete="off">
                </div>

                <div>
                    <button type="submit" class="btn-primary">
                        Verify & Login
                    </button>
                </div>

                <div class="text-center">
                    <a href="/admin/login" class="text-sm text-purple-600 hover:text-purple-700">
                        ← Back to login
                    </a>
                </div>
            </form>

            <div class="mt-6">
                <div class="info-box">
                    <p class="text-xs">
                        Didn't receive the code? Check your spam folder or
                        <a href="/admin/login" class="font-semibold underline">try again</a>
                    </p>
                    <p class="text-xs mt-2">
                        Code expires in 10 minutes
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-submit when 6 digits entered
        document.getElementById('code').addEventListener('input', function(e) {
            // Only allow numbers
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            
            if (e.target.value.length === 6) {
                e.target.form.submit();
            }
        });

        // Prevent paste of non-numeric characters
        document.getElementById('code').addEventListener('paste', function(e) {
            e.preventDefault();
            const pastedText = (e.clipboardData || window.clipboardData).getData('text');
            const numericOnly = pastedText.replace(/[^0-9]/g, '').substring(0, 6);
            e.target.value = numericOnly;
            
            if (numericOnly.length === 6) {
                e.target.form.submit();
            }
        });
    </script>
</body>
</html>
