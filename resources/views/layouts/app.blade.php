<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://www.google.com/recaptcha/api.js?render={{ env('RECAPTCHA_V3_SITE_KEY') }}"></script>
    <script>
        function confirmLogout() {
            // Check if user is on paper submission form
            if (window.location.pathname.includes('/papers/create')) {
                Swal.fire({
                    title: 'Cannot Logout',
                    text: 'Please cancel the paper submission form first, then logout.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return false;
            }
            return true;
        }
    </script>
</head>
<body class="bg-gray-100">
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold">IJRPR</span>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <a href="{{ route('papers.index') }}" class="text-gray-700 hover:text-gray-900">My Papers</a>
                        <a href="{{ route('profile.show') }}" class="text-gray-700 hover:text-gray-900">Profile</a>
                        <span class="text-gray-700">{{ auth()->user()->name }}</span>
                        <form method="POST" action="{{ route('logout') }}" id="logout-form" onsubmit="return confirmLogout()">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">Logout</button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</body>
</html>
