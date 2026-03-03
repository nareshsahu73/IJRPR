@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Submit New Paper</h1>

    @if($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('papers.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Title of Paper *</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Name of Author (Only Corresponding Author) *</label>
            <p class="text-sm text-gray-600 mb-2">
                [All author(s) and co-author(s) must have their <strong>full names</strong> clearly written in the MS Word file of the paper below Title of paper, which you are going to submit]
            </p>
            <input type="text" name="corresponding_author_name" value="{{ old('corresponding_author_name') }}" required
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('corresponding_author_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 mb-2">Email Address *</label>
                <p class="text-sm text-gray-600 mb-2">The email address of the author submitting the paper (Corresponding Author)</p>
                <input type="email" name="corresponding_author_email" value="{{ old('corresponding_author_email') }}" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('corresponding_author_email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Contact No (With Country code) *</label>
                <input type="text" name="contact_no" value="{{ old('contact_no') }}" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('contact_no')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Affiliation of Corresponding Author *</label>
            <p class="text-sm text-gray-600 mb-2">Name of College/University/Company/ of Corresponding Author</p>
            <input type="text" name="affiliation" value="{{ old('affiliation') }}" required
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('affiliation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 mb-2">Position/Post of Author *</label>
                <select name="position" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Choose</option>
                    <option value="UG Student" {{ old('position') == 'UG Student' ? 'selected' : '' }}>UG Student</option>
                    <option value="PG Student" {{ old('position') == 'PG Student' ? 'selected' : '' }}>PG Student</option>
                    <option value="PhD Student" {{ old('position') == 'PhD Student' ? 'selected' : '' }}>PhD Student</option>
                    <option value="Academic Person" {{ old('position') == 'Academic Person' ? 'selected' : '' }}>Academic Person</option>
                    <option value="Industry Person" {{ old('position') == 'Industry Person' ? 'selected' : '' }}>Industry Person</option>
                    <option value="Other" {{ old('position') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('position')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Country Name *</label>
                <input type="text" name="country_name" value="{{ old('country_name') }}" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('country_name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Upload Paper (Submit only MS word file DOC,DOCX file only) *</label>
            <p class="text-sm text-gray-600 mb-2">Upload 1 supported file: document. Max 10 MB.</p>
            <input type="file" name="file" required accept=".doc,.docx"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('file')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 mb-2">Additional Notes (Optional)</label>
            <textarea name="description" rows="3"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                Submit Paper
            </button>
            <a href="{{ route('papers.index') }}" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
