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
            <label class="block text-gray-700 mb-2">Paper Title *</label>
            <input type="text" name="Title" value="{{ old('Title') }}" required
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('Title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Corresponding Author Name *</label>
            <p class="text-sm text-gray-600 mb-2">
                Only One Author Name is required. Name of all authors should be written on MS word file of Paper (Below Title of Paper). Only will get E-certificate
            </p>
            <input type="text" name="author_name" value="{{ old('author_name') }}" required
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('author_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 mb-2">Corresponding Author Email *</label>
                <p class="text-sm text-gray-600 mb-2">The email address of the author submitting the paper (Corresponding Author)</p>
                <input type="email" name="cer_author_name" value="{{ old('cer_author_name') }}" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('cer_author_name')
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
            <label class="block text-gray-700 mb-2">Organization/Institute Name *</label>
            <input type="text" name="affiliation" value="{{ old('affiliation') }}" required
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('affiliation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Higher Qualification *</label>
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

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Attach Paper *</label>
            <p class="text-sm text-gray-600 mb-2">Choose File: No file chosen. Max file size: 10MB</p>
            <input type="file" name="file" required accept=".doc,.docx"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('file')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Author Comment (If Any)(Field)</label>
            <textarea name="Abstract" rows="5"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('Abstract') }}</textarea>
            @error('Abstract')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="flex items-start">
                <input type="checkbox" name="declaration" required class="mt-1 mr-2">
                <span class="text-sm text-gray-700">
                    Important Instruction: I have written name of Author [with all team members/Co-authors/Guide/Mentor (If More than One author)] on Ms word File of paper below "Title of Paper". I know only Author(s) who name written on Paper will get E-certificate
                </span>
            </label>
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
