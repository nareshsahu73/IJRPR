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
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Paper ID</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Title</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Author</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Position</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Submitted</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 text-sm">

                @foreach($papers as $paper)
                <tr class="hover:bg-gray-50">

                    <td class="px-5 py-4">
                        {{ $paper->id }}
                    </td>

                    <td class="px-5 py-4">
                        {{ Str::limit($paper->Title ?? 'N/A', 35) }}
                    </td>

                    <td class="px-5 py-4">
                        {{ $paper->author_name ?? 'N/A' }}
                    </td>

                    <td class="px-5 py-4">
                        <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">
                            {{ $paper->position ?? 'N/A' }}
                        </span>
                    </td>

                    <td class="px-5 py-4">

                        @if($paper->paper_status)

                            <span class="px-2 py-1 text-xs rounded-full

                                {{ $paper->paper_status == 'Paper Accepted' ? 'bg-green-100 text-green-700' : '' }}

                                {{ $paper->paper_status == 'Paper Rejected' ? 'bg-red-100 text-red-700' : '' }}

                                {{ $paper->paper_status == 'Under Review' ? 'bg-yellow-100 text-yellow-700' : '' }}
                            ">
                                {{ $paper->paper_status }}
                            </span>

                        @else

                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                                Pending
                            </span>

                        @endif

                    </td>

                    <td class="px-5 py-4 text-gray-600">
                        {{ \Carbon\Carbon::parse($paper->created_at)->format('Y-m-d H:i') }}
                    </td>

                    <td class="px-5 py-4 space-x-3">
                       <!-- <a href="{{ route('papers.show', $paper) }}" class="text-blue-500 hover:underline mr-3">View</a> -->
                        <a href="{{ route('papers.download', $paper) }}"
                           class="text-green-600 hover:text-green-800 font-medium">
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