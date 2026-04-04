<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Password Reset - IJRPR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); }
        .card { background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .input-field { width:100%; padding:12px 16px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; }
        .input-field:focus { outline:none; border-color:#1e40af; box-shadow:0 0 0 3px rgba(30,64,175,0.1); }
        .btn { width:100%; padding:12px; background:#1e40af; color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer; }
        .btn:hover { background:#1e3a8a; }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="card max-w-md w-full p-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Admin Password Reset</h2>
                <p class="mt-2 text-sm text-gray-600">Enter your admin email to receive a reset link</p>
            </div>

            @if(session('status'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.email') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Admin Email</label>
                    <input type="email" name="email" required class="input-field" placeholder="Enter your admin email" value="{{ old('email') }}">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="btn">Send Reset Link</button>
                <p class="text-center text-sm text-gray-600">
                    <a href="{{ route('admin.login') }}" class="text-blue-700 hover:underline font-semibold">← Back to Admin Login</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html>
