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
            <input type="text" name="author_name" value="{{ old('author_name') }}" required
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('author_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
            <p class="text-red-600 text-sm mt-2 font-medium">
                Only One Author Name is required. Name of all authors should be written on MS word file of Paper (Below Title of Paper). Only will get E-certificate
            </p>
        </div>

        <div class="mb-4">
            <p class="text-sm text-gray-700 mb-2">
                Author can use the email of account holder if they wish (by clicking check box)
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 mb-2">Author Email * (Same as the email of account holder)</label>
                <div class="flex items-center gap-2">
                    <label class="flex items-center whitespace-nowrap">
                        <input type="checkbox" id="use_account_email" class="mr-1">
                        <span class="text-sm text-gray-700"></span>
                    </label>
                    <input type="email" name="cer_author_name" id="cer_author_name" value="{{ old('cer_author_name') }}" required
                        class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
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

        <script>
        // Auto-fill email from logged-in user account
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

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Organization/Institute Name *</label>
            <input type="text" name="affiliation" value="{{ old('affiliation') }}" required
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('affiliation')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 mb-2">Highest Qualification *</label>
                <input type="text" name="highest_qualification" value="{{ old('highest_qualification') }}" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('highest_qualification')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 mb-2">Country *</label>
                <input type="text" name="Keywords" value="{{ old('Keywords') }}" required
                    class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('Keywords')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>


        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Position/Post *</label>
            <select name="position" required
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Choose</option>
                <option value="UG Student" {{ old('position') == 'UG Student' ? 'selected' : '' }}>UG Student</option>
                <option value="PG Student" {{ old('position') == 'PG Student' ? 'selected' : '' }}>PG Student</option>
                <option value="PhD Student" {{ old('position') == 'PhD Student' ? 'selected' : '' }}>PhD Student</option>
                <option value="Academic Person" {{ old('position') == 'Academic Person' ? 'selected' : '' }}>Academic Person</option>
                <option value="Industry Person" {{ old('position') == 'Industry Person' ? 'selected' : '' }}>Industry Person</option>
               <option value="Industry Person" {{ old('position') == 'Research Scholar' ? 'selected' : '' }}>Research Scholar</option>
                <option value="Other" {{ old('position') == 'Other' ? 'selected' : '' }}>Other</option>
            </select>
            @error('position')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Attach Paper *</label>
           <input type="file" name="file" required accept=".docx,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                onchange="validateFile(this)">
            <p id="file-error" class="text-red-500 text-sm mt-1 hidden">Please select a valid DOCX file</p>
            @error('file')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
             <p class="text-red-600 text-sm mt-2 font-medium">Please Upload Word File (.docx) extension only</p>

            </div>

        <script>
        function validateFile(input) {
            const file = input.files[0];
            const errorMsg = document.getElementById('file-error');
            
            if (file) {
                const fileName = file.name.toLowerCase();
                const fileSize = file.size;
                const maxSize = 10 * 1024 * 1024; // 10MB in bytes
                
                // Check file extension
                if (!fileName.endsWith('.docx')) {
                    errorMsg.textContent = 'Only DOCX files are allowed';
                    errorMsg.classList.remove('hidden');
                    input.value = '';
                    return false;
                }
                
                // Check file size
                if (fileSize > maxSize) {
                    errorMsg.textContent = 'Please Upload Word File (.docx) extension only, Maximum Size Allowed 10 MB Only.if file size above 10 MB then send paper to editor@ijrpr.com';
                    errorMsg.classList.remove('hidden');
                    input.value = '';
                    return false;
                }
                
                // Check MIME type
                if (file.type !== 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    errorMsg.textContent = 'Invalid file type. Only DOCX files are allowed';
                    errorMsg.classList.remove('hidden');
                    input.value = '';
                    return false;
                }
                
                errorMsg.classList.add('hidden');
                return true;
            }
        }
        
        // Form validation before submit
        document.querySelector('form').addEventListener('submit', function(e) {
            const fileInput = document.querySelector('input[name="file"]');
            if (!fileInput.files || fileInput.files.length === 0) {
                e.preventDefault();
                document.getElementById('file-error').textContent = 'Please select a file to upload';
                document.getElementById('file-error').classList.remove('hidden');
                fileInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return false;
            }
        });
        </script>

        <div class="mb-4">
            <label class="block text-gray-700 mb-2">Author Comment (If any,Optional)</label>
            <textarea name="Abstract" rows="5"
                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('Abstract') }}</textarea>
            @error('Abstract')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
             <p class="text-red-600 text-sm mt-2 font-medium">
                यहाँ किसी भी Author/Co-author/Guide/Team Member के नाम न लिखें। सभी लेखकों के नाम पेपर की MS Word फाइल पर (below Title of paper) लिखे होने चाहिए। सभी ऑथर्स जिनका नाम पेपर की MS वर्ड फाइल पर लिखा होगा केवल उन्हीं ऑथर्स को सर्टिफिकेट इश्यू होगा
            </p>
        </div>

        <div class="mb-6">
            <label class="flex items-start">
                <input type="checkbox" name="declaration" required class="mt-1 mr-2">
                <span class="text-red-600 text-sm mt-2 font-medium">
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
