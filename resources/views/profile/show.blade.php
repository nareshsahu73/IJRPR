@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">My Profile</h1>
            <a href="{{ route('profile.edit') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Edit Profile
            </a>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-gray-600 text-sm mb-1">Name</label>
                <p class="text-lg">{{ auth()->user()->name }}</p>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mb-1">Email</label>
                <p class="text-lg">{{ auth()->user()->email }}</p>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mb-1">Account Type</label>
                <p class="text-lg">
                    @if(auth()->user()->is_admin)
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">Administrator</span>
                    @else
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">User</span>
                    @endif
                </p>
            </div>

            <div>
                <label class="block text-gray-600 text-sm mb-1">Member Since</label>
                <p class="text-lg">{{ auth()->user()->created_at->format('F d, Y') }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Change Password</h2>
        
        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Current Password</label>
                <div class="relative">
                    <input type="password" name="current_password" id="current_password" required
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10">
                    <button type="button" onclick="togglePassword('current_password')" 
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <svg id="current_password_eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
                @error('current_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">New Password</label>
                <div class="relative">
                    <input type="password" name="password" id="new_password" required
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10">
                    <button type="button" onclick="togglePassword('new_password')" 
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <svg id="new_password_eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mt-1">
                    Password must be at least 12 characters and include uppercase, lowercase, numbers, and special characters (!@#$%^&*)
                </p>
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Confirm New Password</label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 pr-10">
                    <button type="button" onclick="togglePassword('password_confirmation')" 
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <svg id="password_confirmation_eye" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                Update Password
            </button>
        </form>
    </div>

    <script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const eye = document.getElementById(fieldId + '_eye');
        
        if (field.type === 'password') {
            field.type = 'text';
            eye.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
            `;
        } else {
            field.type = 'password';
            eye.innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            `;
        }
    }
    </script>
</div>
@endsection
