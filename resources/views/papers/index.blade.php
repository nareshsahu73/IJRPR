@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">My Papers</h1>

        <a href="{{ route('papers.create') }}"
           class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm">
            Submit New Paper
        </a>
    </div>

    @if($papers->isEmpty())
        <p class="text-gray-500 text-center py-8">No papers submitted yet.</p>
    @else

    <div class="overflow-x-auto">
        <table class="min-w-full border border-gray-200 rounded-lg">
            
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase" style="width:80px">Paper ID</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase" style="width:200px">Title</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase" style="width:160px">Author</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase" style="width:110px">Position</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase" style="width:120px">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase" style="width:130px">Submitted</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase" style="width:120px">Submitted Paper</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 text-sm">

                @foreach($papers as $paper)
                <tr class="hover:bg-gray-50">

                    <td class="px-5 py-4">
                        {{ $paper->id }}
                    </td>

                    <td class="px-5 py-4" style="max-width:200px">
                        <div class="truncate" title="{{ $paper->Title ?? 'N/A' }}">
                            {{ e(Str::limit($paper->Title ?? 'N/A', 35)) }}
                        </div>
                    </td>

                    <td class="px-5 py-4" style="max-width:160px">
                        <div class="truncate" title="{{ $paper->author_name ?? 'N/A' }}">
                            {{ e(Str::limit($paper->author_name ?? 'N/A', 25)) }}
                        </div>
                    </td>

                    <td class="px-5 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                            {{ e($paper->position ?? 'N/A') }}
                        </span>
                    </td>

                    <td class="px-5 py-4">
                        @php
                            $status = $paper->paper_status;
                            $badge = match($status) {
                                'PaperAccepted', 'Paper Accepted'           => ['bg-green-100 text-green-700',  'Paper Accepted'],
                                'PaperRejected', 'Paper Rejected'           => ['bg-red-100 text-red-700',      'Paper Rejected'],
                                'PaperUnderReview', 'Under Review'          => ['bg-yellow-100 text-yellow-700','Under Review'],
                                'PaperPublished', 'Paper Published'         => ['bg-blue-100 text-blue-700',    'Paper Published'],
                                'PaperPublishedWithDOI'                     => ['bg-indigo-100 text-indigo-700','Paper Published with DOI'],
                                'PaymentReceived'                           => ['bg-purple-100 text-purple-700','Payment Received'],
                                'CommentsToUser'                            => ['bg-orange-100 text-orange-700','Comments to User'],
                                'Paper Withdraw'                            => ['bg-gray-100 text-gray-600',    'Paper Withdraw'],
                                default                                     => ['bg-gray-100 text-gray-600',    $status ?? 'Pending'],
                            };
                        @endphp
                        @if($status === 'PaperPublishedWithDOI')
                        <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-700">Paper Published with DOI</span>
                        @else
                        <span class="px-2 py-1 text-xs rounded-full {{ $badge[0] }}">
                            {{ $badge[1] }}
                        </span>
                        @endif
                    </td>

                    <td class="px-5 py-4 text-gray-600">
                        {{ \Carbon\Carbon::parse($paper->created_at)->format('Y-m-d H:i') }}
                    </td>

                    <td class="px-5 py-4 whitespace-nowrap" style="width:120px">
                        <a href="{{ route('papers.download', $paper) }}"
                           class="text-green-600 hover:text-green-800 font-medium mr-3">
                            Download
                        </a>
                        <a href="{{ route('papers.status', $paper) }}"
                           class="text-purple-600 hover:text-purple-800 font-medium">
                            Status
                        </a>
                    </td>

                </tr>
                @endforeach

            </tbody>

        </table>
    </div>

    @endif

</div>
@endsection