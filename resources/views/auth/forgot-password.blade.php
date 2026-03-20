<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - IJRPR</title>
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
            font-size: 14px;
            transition: all 0.2s;
        }
        .input-field:focus {
            outline: none;
            border-color: #8b5cf6;
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
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
        .error-box {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
        }
        .success-box {
            background: #d1fae5;
            border: 1px solid #a7f3d0;
            color: #065f46;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="login-card max-w-md w-full p-8">
            <div class="text-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">
                    Forgot Password
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Enter your email to receive a password reset link
                </p>
            </div>

            <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
                @csrf

                @if(session('status'))
                    <div class="success-box">
                        {{ session('status') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="error-box">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email address<span class="text-red-500">*</span>
                    </label>
                    <input id="email" name="email" type="email" required
                        class="input-field"
                        placeholder="Enter your email"
                        value="{{ old('email') }}">
                </div>

                <div>
                    <button type="submit" class="btn-primary">
                        Send Reset Link
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('login') }}" class="text-sm text-purple-600 hover:text-purple-700">
                        ← Back to login
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
