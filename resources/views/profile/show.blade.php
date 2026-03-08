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
                <input type="password" name="current_password" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('current_password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">New Password</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                Update Password
            </button>
        </form>
    </div>
</div>
@endsection
