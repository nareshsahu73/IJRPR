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

    <form method="POST" action="{{ route('papers.store') }}" enctype="multipart/form-data" novalidate id="paperForm">
        @csrf

        <div class="mb-6">
            <label class="block text-gray-700 mb-2 font-bold">Paper Title *</label>
            <input type="text" name="Title" id="Title" value="{{ old('Title') }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                onblur="validateField(this, 'title-error', 'Paper Title is required')">
            <p id="title-error" class="text-red-500 text-sm mt-1 hidden"></p>
            @error('Title')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 mb-2 font-bold">Corresponding Author Name *</label>
            <input type="text" name="author_name" id="author_name" value="{{ old('author_name') }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                onblur="validateField(this, 'author-error', 'Corresponding Author Name is required')">
            <p id="author-error" class="text-red-500 text-sm mt-1 hidden"></p>
            @error('author_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            <p class="text-red-600 text-sm mt-2 font-medium">
                Only One Author Name is required here. Name of all author(s) should be written on MS word file of Paper (Below Title of Paper). They will get E-certificates
            </p>
        </div>

        <div class="mb-6">
            <p class="text-sm text-gray-700 mb-2">Author can use the email of account holder if they wish (by clicking check box)</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-gray-700 mb-2 font-bold">Author Email * (Same as the email of account holder)</label>
                <div class="flex items-center gap-2">
                    <label class="flex items-center whitespace-nowrap">
                        <input type="checkbox" id="use_account_email" class="mr-1">
                        <span class="text-sm text-gray-700"></span>
                    </label>
                    <input type="email" name="cer_author_name" id="cer_author_name" value="{{ old('cer_author_name') }}"
                        class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        onblur="validateEmail(this, 'email-error')">
                </div>
                <p id="email-error" class="text-red-500 text-sm mt-1 hidden"></p>
                @error('cer_author_name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-gray-700 mb-2 font-bold">Contact No (With Country code) *</label>
                <input type="text" name="contact_no" id="contact_no" value="{{ old('contact_no') }}"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onblur="validateField(this, 'contact-error', 'Contact No is required')">
                <p id="contact-error" class="text-red-500 text-sm mt-1 hidden"></p>
                @error('contact_no')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <script>
        document.getElementById('use_account_email').addEventListener('change', function() {
            const emailInput = document.getElementById('cer_author_name');
            if (this.checked) {
                emailInput.value = '{{ auth()->user()->email }}';
                emailInput.readOnly = true;
                emailInput.classList.add('bg-gray-100');
            } else {
                emailInput.value = '{{ old('cer_author_name') }}';
                emailInput.readOnly = false;
                emailInput.classList.remove('bg-gray-100');
            }
        });
        </script>

        <div class="mb-6">
            <label class="block text-gray-700 mb-2 font-bold">Organization/Institute Name *</label>
            <input type="text" name="affiliation" id="affiliation" value="{{ old('affiliation') }}"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                onblur="validateField(this, 'affiliation-error', 'Organization/Institute Name is required')">
            <p id="affiliation-error" class="text-red-500 text-sm mt-1 hidden"></p>
            @error('affiliation')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-gray-700 mb-2 font-bold">Highest Qualification *</label>
                <input type="text" name="highest_qualification" id="highest_qualification" value="{{ old('highest_qualification') }}"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onblur="validateField(this, 'qualification-error', 'Highest Qualification is required')">
                <p id="qualification-error" class="text-red-500 text-sm mt-1 hidden"></p>
                @error('highest_qualification')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-gray-700 mb-2 font-bold">Country *</label>
                <input type="text" name="Keywords" id="Keywords" value="{{ old('Keywords') }}"
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onblur="validateField(this, 'country-error', 'Country is required')">
                <p id="country-error" class="text-red-500 text-sm mt-1 hidden"></p>
                @error('Keywords')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 mb-2 font-bold">Position/Post *</label>
            <select name="position" id="position"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                onblur="validateSelect(this, 'position-error', 'Position/Post is required')">
                <option value="">Choose</option>
                <option value="UG Student" {{ old('position') == 'UG Student' ? 'selected' : '' }}>UG Student</option>
                <option value="PG Student" {{ old('position') == 'PG Student' ? 'selected' : '' }}>PG Student</option>
                <option value="PhD Student" {{ old('position') == 'PhD Student' ? 'selected' : '' }}>PhD Student</option>
                <option value="Academic Person" {{ old('position') == 'Academic Person' ? 'selected' : '' }}>Academic Person</option>
                <option value="Industry Person" {{ old('position') == 'Industry Person' ? 'selected' : '' }}>Industry Person</option>
                <option value="Research Scholar" {{ old('position') == 'Research Scholar' ? 'selected' : '' }}>Research Scholar</option>
                <option value="Other" {{ old('position') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            <p id="position-error" class="text-red-500 text-sm mt-1 hidden"></p>
            @error('position')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 mb-2 font-bold">Attach Paper *</label>
            <input type="file" name="file" id="file" accept=".docx"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                onchange="validateFile(this)">
            <p class="text-red-600 text-sm mt-1 font-medium">Please Upload Word File (.docx) extension only, Maximum Size Allowed 15 MB Only. If file size above 15 MB then send paper to editor@ijrpr.com</p>
            <p id="file-error" class="text-red-500 text-sm mt-1 hidden"></p>
            @error('file')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 mb-2 font-bold">Author Comment (If any,Optional)</label>
            <textarea name="author_comment" id="author_comment" rows="3"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('author_comment') }}</textarea>
        </div>

        <div class="mb-6">
            <label class="flex items-start gap-2">
                <input type="checkbox" id="declaration" name="declaration" class="mt-1" onchange="validateDeclaration()">
                <span class="text-sm text-gray-700">
                    I have written name of Author [with all teammembers/Co-authors/Guide/Mentor (If More than One author)] on Ms word File of paper below "Title of Paper". I know only Author(s) who name written on Paper will get E-certificate. I agree to the terms of service and privacy policy *
                </span>
            </label>
            <p id="declaration-error" class="text-red-500 text-sm mt-1 hidden">You must agree to the declaration before submitting.</p>
        </div>

        <div class="flex gap-4">
            <button type="submit" id="submitBtn"
                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                Submit Paper
            </button>
            <a href="{{ route('papers.index') }}"
                class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
function validateField(input, errorId, message) {
    const error = document.getElementById(errorId);
    if (!input.value.trim()) {
        error.textContent = message;
        error.classList.remove('hidden');
        input.classList.add('border-red-500');
        return false;
    } else {
        error.classList.add('hidden');
        input.classList.remove('border-red-500');
        return true;
    }
}

function validateEmail(input, errorId) {
    const error = document.getElementById(errorId);
    const val = input.value.trim();
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!val) {
        error.textContent = 'Author Email is required.';
        error.classList.remove('hidden');
        input.classList.add('border-red-500');
        return false;
    } else if (!emailRegex.test(val)) {
        error.textContent = 'Please enter a valid email address.';
        error.classList.remove('hidden');
        input.classList.add('border-red-500');
        return false;
    } else {
        error.classList.add('hidden');
        input.classList.remove('border-red-500');
        return true;
    }
}

function validateSelect(select, errorId, message) {
    const error = document.getElementById(errorId);
    if (!select.value) {
        error.textContent = message;
        error.classList.remove('hidden');
        select.classList.add('border-red-500');
        return false;
    } else {
        error.classList.add('hidden');
        select.classList.remove('border-red-500');
        return true;
    }
}

function validateFile(input) {
    const error = document.getElementById('file-error');
    if (!input.files || !input.files[0]) {
        error.textContent = 'Please attach your paper file.';
        error.classList.remove('hidden');
        return false;
    }
    const file = input.files[0];
    const ext = file.name.split('.').pop().toLowerCase();
    const maxSize = 15 * 1024 * 1024; // 15 MB

    if (ext !== 'docx') {
        error.textContent = 'Only .docx files are allowed.';
        error.classList.remove('hidden');
        input.value = '';
        return false;
    }
    if (file.size > maxSize) {
        error.classList.add('hidden');
        input.value = '';
        Swal.fire({
            icon: 'error',
            title: 'File Too Large',
            text: 'Please Upload Word File (.docx) extension only, Maximum Size Allowed 15 MB Only. If file size above 15 MB then send paper to editor@ijrpr.com',
        });
        return false;
    }
    error.classList.add('hidden');
    return true;
}

function validateDeclaration() {
    const cb = document.getElementById('declaration');
    const error = document.getElementById('declaration-error');
    if (!cb.checked) {
        error.classList.remove('hidden');
        return false;
    } else {
        error.classList.add('hidden');
        return true;
    }
}

document.getElementById('paperForm').addEventListener('submit', function(e) {
    let valid = true;

    if (!validateField(document.getElementById('Title'), 'title-error', 'Paper Title is required.')) valid = false;
    if (!validateField(document.getElementById('author_name'), 'author-error', 'Corresponding Author Name is required.')) valid = false;
    if (!validateEmail(document.getElementById('cer_author_name'), 'email-error')) valid = false;
    if (!validateField(document.getElementById('contact_no'), 'contact-error', 'Contact No is required.')) valid = false;
    if (!validateField(document.getElementById('affiliation'), 'affiliation-error', 'Organization/Institute Name is required.')) valid = false;
    if (!validateField(document.getElementById('highest_qualification'), 'qualification-error', 'Highest Qualification is required.')) valid = false;
    if (!validateField(document.getElementById('Keywords'), 'country-error', 'Country is required.')) valid = false;
    if (!validateSelect(document.getElementById('position'), 'position-error', 'Position/Post is required.')) valid = false;
    if (!validateFile(document.getElementById('file'))) valid = false;
    if (!validateDeclaration()) valid = false;

    if (!valid) {
        e.preventDefault();
        // Scroll to first error
        const firstError = document.querySelector('.border-red-500, [id$="-error"]:not(.hidden)');
        if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});
</script>
@endsection
