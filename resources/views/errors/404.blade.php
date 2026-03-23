<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found - IJRPR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-md w-full bg-white rounded-lg shadow-lg p-8 text-center">
            <!-- Icon -->
            <div class="mb-6">
                <svg class="mx-auto h-24 w-24 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>

            <!-- Error Code -->
            <h1 class="text-6xl font-bold text-gray-800 mb-4">404</h1>
            
            <!-- Error Message -->
            <h2 class="text-2xl font-semibold text-gray-700 mb-4">Page Not Found</h2>
            
            <p class="text-gray-600 mb-6">
                The page you are looking for doesn't exist or has been moved.
            </p>

            <!-- Action Buttons -->
            <div class="space-y-3">
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ url('/myweb') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                            Go to Admin Panel
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                            Go to Dashboard
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                        Login
                    </a>
                @endauth

                <a href="/" class="block w-full text-gray-600 hover:text-gray-800 font-medium py-2 transition duration-200">
                    ← Back to Home
                </a>
            </div>
        </div>
    </div>
</body>
</html>
