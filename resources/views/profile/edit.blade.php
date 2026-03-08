@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Edit Profile</h1>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Name *</label>
            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 mb-2">Email *</label>
            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('email')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                Update Profile
            </button>
            <a href="{{ route('profile.show') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
