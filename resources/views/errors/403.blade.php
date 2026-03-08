<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - IJRPR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Icon -->
            <div class="mb-6">
                <svg class="mx-auto h-24 w-24 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <!-- Error Code -->
            <h1 class="text-6xl font-bold text-gray-800 mb-4">403</h1>
            
            <!-- Error Message -->
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Access Denied</h2>
            
            <p class="text-gray-600 mb-6">
                {{ $exception->getMessage() ?: 'You do not have permission to access this area. This section is restricted to administrators only.' }}
            </p>

            <!-- User Info -->
            @auth
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <p class="text-sm text-blue-800">
                        <strong>Logged in as:</strong> {{ auth()->user()->name }} ({{ auth()->user()->email }})
                    </p>
                    <p class="text-xs text-blue-600 mt-1">
                        You are logged in as a regular user. Admin access is required.
                    </p>
                </div>
            @endauth

            <!-- Action Buttons -->
            <div class="space-y-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                        Go to Dashboard
                    </a>
                    
                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <button type="submit" class="w-full bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-lg transition duration-200">
                            Logout & Login as Admin
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                        Login
                    </a>
                @endauth

                <a href="/" class="block w-full text-gray-600 hover:text-gray-800 font-medium py-2 transition duration-200">
                    ← Back to Home
                </a>
            </div>

            <!-- Help Text -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    Need admin access? Contact your system administrator.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
