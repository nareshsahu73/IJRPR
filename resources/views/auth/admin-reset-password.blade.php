<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Reset Password - IJRPR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); }
        .card { background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); }
        .input-field { width:100%; padding:12px 16px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px; }
        .input-field:focus { outline:none; border-color:#1e40af; box-shadow:0 0 0 3px rgba(30,64,175,0.1); }
        .btn { width:100%; padding:12px; background:#1e40af; color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer; }
        .btn:hover { background:#1e3a8a; }
        .input-wrapper { position:relative; }
        .eye-btn { position:absolute; right:12px; top:50%; transform:translateY(-50%); cursor:pointer; color:#9ca3af; background:none; border:none; }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="card max-w-md w-full p-8">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-bold text-gray-900">Set New Admin Password</h2>
            </div>

            @if($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.update') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <div class="input-wrapper">
                        <input id="password" type="password" name="password" required class="input-field" placeholder="Enter new password" autocomplete="new-password">
                        <button type="button" class="eye-btn" onclick="toggle('password')">👁</button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <div class="input-wrapper">
                        <input id="password_confirmation" type="password" name="password_confirmation" required class="input-field" placeholder="Confirm new password" autocomplete="new-password">
                        <button type="button" class="eye-btn" onclick="toggle('password_confirmation')">👁</button>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-200 rounded p-3 text-xs text-gray-600">
                    <strong>Requirements:</strong> Min 15 chars, uppercase, lowercase, number, special character (!@#$%^&*)
                </div>

                <button type="submit" class="btn">Reset Password</button>
            </form>
        </div>
    </div>
    <script>
        function toggle(id) {
            const f = document.getElementById(id);
            f.type = f.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>
