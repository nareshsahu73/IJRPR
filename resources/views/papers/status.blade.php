@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Paper Status</h1>
        <a href="{{ route('papers.index') }}" class="text-blue-500 hover:underline">← Back to My Papers</a>
    </div>

    <div class="bg-gray-50 p-6 rounded-lg mb-6">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-600">Paper ID</p>
                <p class="font-semibold">IJRPR-{{ $paper->id }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Paper Title</p>
                <p class="font-semibold">{{ $paper->Title }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Author Name</p>
                <p class="font-semibold">{{ $paper->author_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Submission Date</p>
                <p class="font-semibold">{{ $paper->created_at }}</p>
            </div>
        </div>
    </div>

    @if($paper->paper_status == 'PaperUnderReview')
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded">
            <h2 class="text-xl font-bold text-yellow-800 mb-4">Paper Status: Paper Under Review</h2>
            <div class="text-gray-700 space-y-4">
                <p>Dear Author,</p>
                <p>Thank you for submitting your paper to <strong>International Journal of Research Publication and Reviews (IJRPR)</strong>.</p>
                <div class="bg-white p-4 rounded border">
                    <p><strong>Paper ID:</strong> IJRPR-{{ $paper->id }}</p>
                    <p><strong>Paper Title:</strong> {{ $paper->Title }}</p>
                </div>
                <p>Your paper is currently under review. Once the review process is completed, we will notify you through your registered email.</p>
                <div class="mt-6">
                    <p>With Warm Regards</p>
                    <p class="font-semibold">Editor-in-Chief</p>
                    <p>International Journal of Research Publication and Reviews</p>
                    <p><a href="http://www.ijrpr.com" target="_blank" class="text-blue-500 hover:underline">http://www.ijrpr.com</a></p>
                </div>
            </div>
        </div>

    @elseif($paper->paper_status == 'PaperAccepted')
        <div class="bg-green-50 border-l-4 border-green-500 p-6 rounded">
            <h2 class="text-xl font-bold text-green-800 mb-4">Paper Status: Paper Accepted</h2>
            <div class="text-gray-700 space-y-4">
                <p>Dear {{ $paper->author_name }},</p>
                <p>This is to inform you that your paper has been accepted for publication in <strong>International Journal of Research Publication and Reviews (IJRPR)</strong> and will be published in the current issue.</p>
                <div class="bg-white p-4 rounded border">
                    <p><strong>Paper ID:</strong> IJRPR-{{ $paper->id }}</p>
                    <p><strong>Paper Title:</strong> {{ $paper->Title }}</p>
                </div>
                <p>You are advised to complete the following steps for publication of your research paper:</p>
                <div class="bg-white p-4 rounded border">
                    <h3 class="font-bold text-lg mb-2">Step 1: Submit Copyright Form</h3>
                    <p class="mb-2">Download the copyright form from the website and send it to us after filling and signing.</p>
                    <p class="mb-2"><strong>Download here:</strong> <a href="https://www.ijrpr.com/download/COPY-RIGHT-FORM.pdf" target="_blank" class="text-blue-500 hover:underline">https://www.ijrpr.com/download/COPY-RIGHT-FORM.pdf</a></p>
                    <p class="mb-2"><strong>Note:</strong> Take a print of the form, fill it, scan it and send it to <a href="mailto:contactusijrpr@gmail.com" class="text-blue-500">contactusijrpr@gmail.com</a></p>
                    <p>You can also submit the online copyright form here: <a href="https://forms.gle/s95xHMivEBYgmmSy8" target="_blank" class="text-blue-500 hover:underline">https://forms.gle/s95xHMivEBYgmmSy8</a></p>
                </div>
                <div class="bg-white p-4 rounded border">
                    <h3 class="font-bold text-lg mb-2">Step 2: Submit Publication Fee</h3>
                    <p class="mb-2">Send the payment receipt along with the copyright form to <a href="mailto:contactusijrpr@gmail.com" class="text-blue-500">contactusijrpr@gmail.com</a></p>
                    <p class="mb-2"><strong>Do not forget to mention your Paper ID in the subject.</strong></p>
                    <div class="mt-3">
                        <p class="font-semibold">Publication Fee</p>
                        <ul class="list-disc list-inside ml-4">
                            <li>International Authors: 17 US Dollars</li>
                            <li>Indian Authors: Rs. 599</li>
                            <li>E-Certificate: Free</li>
                        </ul>
                    </div>
                </div>
                <div class="bg-blue-50 p-4 rounded border border-blue-200">
                    <h3 class="font-bold text-lg mb-2">Publication Process</h3>
                    <p class="mb-2">Paper will be published within 24 to 36 working hours after completion of the above steps.</p>
                    <p>All authors will receive an individual soft copy certificate.</p>
                </div>
                <div class="mt-6">
                    <p>With Warm Regards</p>
                    <p class="font-semibold">IJRPR Team</p>
                    <p>International Journal of Research Publication and Reviews</p>
                    <p><a href="http://www.ijrpr.com" target="_blank" class="text-blue-500 hover:underline">http://www.ijrpr.com</a></p>
                </div>
            </div>
        </div>

    @elseif($paper->paper_status == 'PaymentReceived')
        <div class="bg-purple-50 border-l-4 border-purple-500 p-6 rounded">
            <h2 class="text-xl font-bold text-purple-800 mb-4">Paper Status: Payment Received</h2>
            <div class="text-gray-700 space-y-3">
                <p>Dear <strong>{{ $paper->author_name }},</strong></p>
                <p>This is to inform you that the publication fee for your paper has been received.</p>
                <div class="bg-white p-4 rounded border">
                    <p><strong>Paper ID:</strong> IJRPR-{{ $paper->id }}</p>
                    <p><strong>Paper Title:</strong> {{ $paper->Title }}</p>
                </div>
                <p>Your paper will be published within <strong>24 to 36 working hours</strong>.</p>
                <div class="mt-4">
                    <p>With Warm Regards</p>
                    <p class="font-semibold">IJRPR Team</p>
                    <p><strong>International Journal of Research Publication and Reviews (IJRPR)</strong></p>
                    <p><a href="http://www.ijrpr.com" target="_blank" class="text-blue-500 hover:underline">http://www.ijrpr.com</a></p>
                </div>
            </div>
        </div>

    @elseif($paper->paper_status == 'CommentsToUser')
        <div class="bg-orange-50 border-l-4 border-orange-500 p-6 rounded">
            <h2 class="text-xl font-bold text-orange-800 mb-4">Paper Status: Comments to User</h2>
            <div class="text-gray-700 space-y-3">
                <p>Dear <strong>{{ $paper->author_name }},</strong></p>
                <p>Thanks for submitting the paper in IJRPR. Your <strong>Paper ID</strong> is <strong>IJRPR-{{ $paper->id }}.</strong></p>
                <p><strong>Paper Title:</strong> {{ $paper->Title }}</p>
                @if($paper->more_data)
                <div class="bg-white p-4 rounded border border-orange-200">
                    <p class="font-semibold mb-1">Reviewer Comments:</p>
                    <p>{{ $paper->more_data }}</p>
                </div>
                @endif
                <div class="mt-4">
                    <p>With Warm Regards</p>
                    <p class="font-semibold">Editor-In-Chief</p>
                    <p><strong>International Journal of Research Publication and Reviews (IJRPR)</strong></p>
                    <p><a href="http://www.ijrpr.com" target="_blank" class="text-blue-500 hover:underline">http://www.ijrpr.com</a></p>
                </div>
            </div>
        </div>

    @elseif($paper->paper_status == 'PaperRejected')
        <div class="bg-red-50 border-l-4 border-red-500 p-6 rounded">
            <h2 class="text-xl font-bold text-red-800 mb-4">Paper Status: Paper Rejected</h2>
            <div class="text-gray-700 space-y-4">
                <p>Dear Author,</p>
                <p>Thank you for submitting your paper to <strong>International Journal of Research Publication and Reviews (IJRPR)</strong>.</p>
                <div class="bg-white p-4 rounded border">
                    <p><strong>Paper ID:</strong> IJRPR-{{ $paper->id }}</p>
                    <p><strong>Paper Title:</strong> {{ $paper->Title }}</p>
                </div>
                @if($paper->more_data)
                <div class="bg-white p-4 rounded border border-red-200">
                    <p class="font-semibold mb-2">Reviewer Comments:</p>
                    <p>{{ $paper->more_data }}</p>
                </div>
                @endif
                <p>Unfortunately, we cannot publish the paper in its current form. You may revise the content according to the reviewer comments and resubmit.</p>
                <div class="mt-6">
                    <p>With Warm Regards</p>
                    <p class="font-semibold">Editor-in-Chief</p>
                    <p>International Journal of Research Publication and Reviews</p>
                    <p><a href="http://www.ijrpr.com" target="_blank" class="text-blue-500 hover:underline">http://www.ijrpr.com</a></p>
                </div>
            </div>
        </div>

    @elseif($paper->paper_status == 'Paper Withdraw')
        <div class="bg-gray-50 border-l-4 border-gray-500 p-6 rounded">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Paper Status: Paper Withdrawn</h2>
            <div class="text-gray-700 space-y-3">
                <p>Dear <strong>{{ $paper->author_name }},</strong></p>
                <p>Your paper has been withdrawn from the review process.</p>
                <div class="bg-white p-4 rounded border">
                    <p><strong>Paper ID:</strong> IJRPR-{{ $paper->id }}</p>
                    <p><strong>Paper Title:</strong> {{ $paper->Title }}</p>
                </div>
                <p>Manuscript withdrawn at the request of the author(s).</p>
                <div class="mt-4">
                    <p>With Warm Regards</p>
                    <p class="font-semibold">Editor-In-Chief</p>
                    <p><strong>International Journal of Research Publication and Reviews (IJRPR)</strong></p>
                    <p><a href="http://www.ijrpr.com" target="_blank" class="text-blue-500 hover:underline">http://www.ijrpr.com</a></p>
                </div>
            </div>
        </div>

    @elseif($paper->paper_status == 'PaperPublished')
        <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded">
            <h2 class="text-xl font-bold text-blue-800 mb-4">Paper Status: Paper Published</h2>
            <div class="text-gray-700 space-y-4">
                <p>Dear <strong>{{ $paper->author_name }},</strong></p>
                <p>We are pleased to inform you that your paper has been successfully published in <strong>International Journal of Research Publication and Reviews (IJRPR)</strong>.</p>
                <div class="bg-white p-4 rounded border">
                    <p><strong>Paper ID:</strong> IJRPR-{{ $paper->id }}</p>
                    <p><strong>Paper Title:</strong> {{ $paper->Title }}</p>
                    @if($paper->Volume && $paper->Issue)
                    <p><strong>Published in:</strong> Volume {{ $paper->Volume }}, Issue {{ $paper->Issue }}</p>
                    @endif
                    @if($paper->Reference)
                    <p><strong>Article Link:</strong> <a href="{{ $paper->Reference }}" target="_blank" class="text-blue-500 hover:underline">{{ $paper->Reference }}</a></p>
                    @endif
                    @if($paper->certificate_link)
                    <p><strong>Certificate Link:</strong> <a href="{{ $paper->certificate_link }}" target="_blank" class="text-blue-500 hover:underline">{{ $paper->certificate_link }}</a></p>
                    @endif
                </div>
                <p>You can also access it through: <strong>www.ijrpr.com → Menu → Archives → Current Issue</strong></p>
                <div class="mt-6">
                    <p>With Warm Regards</p>
                    <p class="font-semibold">IJRPR Team</p>
                    <p>International Journal of Research Publication and Reviews</p>
                    <p><a href="http://www.ijrpr.com" target="_blank" class="text-blue-500 hover:underline">http://www.ijrpr.com</a></p>
                </div>
            </div>
        </div>

    @elseif($paper->paper_status == 'PaperPublishedWithoutDOI')
        <div class="bg-teal-50 border-l-4 border-teal-500 p-6 rounded">
            <h2 class="text-xl font-bold text-teal-800 mb-4">Paper Status: Paper Published without DOI</h2>
            <div class="text-gray-700 space-y-4">
                <p>Dear <strong>{{ $paper->author_name }},</strong></p>
                <p>We are pleased to inform you that your paper has been successfully published in <strong>International Journal of Research Publication and Reviews (IJRPR)</strong>.</p>
                <div class="bg-white p-4 rounded border">
                    <p><strong>Paper ID:</strong> IJRPR-{{ $paper->id }}</p>
                    <p><strong>Paper Title:</strong> {{ $paper->Title }}</p>
                    @if($paper->Volume && $paper->Issue)
                    <p><strong>Published in:</strong> Volume {{ $paper->Volume }}, Issue {{ $paper->Issue }}</p>
                    @endif
                    @if($paper->Reference)
                    <p><strong>Article Link:</strong> <a href="{{ $paper->Reference }}" target="_blank" class="text-blue-500 hover:underline">{{ $paper->Reference }}</a></p>
                    @endif
                    @if($paper->certificate_link)
                    <p><strong>Certificate Link:</strong> <a href="{{ $paper->certificate_link }}" target="_blank" class="text-blue-500 hover:underline">{{ $paper->certificate_link }}</a></p>
                    @endif
                </div>
                <p>You can also access it through: <strong>www.ijrpr.com → Menu → Archives → Current Issue</strong></p>
                <div class="bg-yellow-50 border border-yellow-200 p-3 rounded text-sm text-gray-600">
                    <strong>Note:</strong> DOI has not been assigned to this paper yet. It will be updated once the DOI is allotted.
                </div>
                <div class="mt-6">
                    <p>With Warm Regards</p>
                    <p class="font-semibold">IJRPR Team</p>
                    <p><strong>International Journal of Research Publication and Reviews (IJRPR)</strong></p>
                    <p><a href="http://www.ijrpr.com" target="_blank" class="text-blue-500 hover:underline">http://www.ijrpr.com</a></p>
                </div>
            </div>
        </div>

    @elseif($paper->paper_status == 'PaperPublishedWithDOI')
        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-6 rounded">
            <h2 class="text-xl font-bold text-indigo-800 mb-4">Paper Status: Paper Published with DOI</h2>
            <div class="text-gray-700 space-y-3">
                <p>Dear <strong>{{ $paper->author_name }},</strong></p>
                <p>For any future communication you are advised to refer your <strong>Paper ID IJRPR-{{ $paper->id }}.</strong></p>
                <div class="bg-white p-4 rounded border">
                    <p><strong>Paper Title:</strong> "{{ $paper->Title }}"</p>
                    @if($paper->Volume && $paper->Issue)
                    <p><strong>Published in:</strong> Volume {{ $paper->Volume }}, Issue {{ $paper->Issue }}</p>
                    @endif
                    @if($paper->DOI)
                    <p><strong>DOI:</strong> {{ $paper->DOI }}</p>
                    @endif
                    @if($paper->Reference)
                    <p><strong>Article Link:</strong> <a href="{{ $paper->Reference }}" class="text-blue-600 underline" target="_blank">{{ $paper->Reference }}</a></p>
                    @endif
                    @if($paper->certificate_link)
                    <p><strong>Certificate Link:</strong> <a href="{{ $paper->certificate_link }}" class="text-blue-600 underline" target="_blank">{{ $paper->certificate_link }}</a></p>
                    @endif
                </div>
                <p class="text-sm text-gray-500 bg-blue-50 border border-blue-200 p-3 rounded">
                    <strong>Note:</strong> If the DOI on the paper is not visible online, it may be due to a cached/old PDF in your browser. Press <strong>Ctrl + F5</strong> or open the link in a new browser to view the latest version.
                </p>
                <div class="mt-4">
                    <p>With Warm Regards</p>
                    <p class="font-semibold">IJRPR Team</p>
                    <p><strong>International Journal of Research Publication and Reviews (IJRPR)</strong></p>
                    <p><a href="http://www.ijrpr.com" target="_blank" class="text-blue-500 hover:underline">http://www.ijrpr.com</a></p>
                </div>
            </div>
        </div>

    @else
        {{-- Fallback for any unknown/new status --}}
        <div class="bg-yellow-50 border-l-4 border-yellow-500 p-6 rounded">
            <h2 class="text-xl font-bold text-yellow-800 mb-4">Paper Status: Under Review</h2>
            <div class="text-gray-700 space-y-4">
                <p>Dear Author,</p>
                <p>Thank you for submitting your paper to <strong>International Journal of Research Publication and Reviews (IJRPR)</strong>.</p>
                <div class="bg-white p-4 rounded border">
                    <p><strong>Paper ID:</strong> IJRPR-{{ $paper->id }}</p>
                    <p><strong>Paper Title:</strong> {{ $paper->Title }}</p>
                </div>
                <p>Your paper is currently under review. Once completed, we will notify you through your registered email.</p>
                <div class="mt-6">
                    <p>With Warm Regards</p>
                    <p class="font-semibold">Editor-in-Chief</p>
                    <p>International Journal of Research Publication and Reviews</p>
                    <p><a href="http://www.ijrpr.com" target="_blank" class="text-blue-500 hover:underline">http://www.ijrpr.com</a></p>
                </div>
            </div>
        </div>
    @endif

    <div class="mt-6 text-center">
        <a href="{{ route('papers.index') }}" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 inline-block">
            Back to My Papers
        </a>
    </div>
</div>
@endsection
