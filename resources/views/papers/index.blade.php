@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">My Papers</h1>
        <a href="{{ route('papers.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
            Submit New Paper
        </a>
    </div>

    @if($papers->isEmpty())
        <p class="text-gray-500 text-center py-8">No papers submitted yet.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Position</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($papers as $paper)
                        <tr>
                            <td class="px-6 py-4">{{ Str::limit($paper->Title ?? 'N/A', 40) }}</td>
                            <td class="px-6 py-4">{{ $paper->author_name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ $paper->position ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $paper->created_at }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('papers.show', $paper) }}" 
                                   class="text-blue-500 hover:underline mr-3">View</a>
                                <a href="{{ route('papers.download', $paper) }}" 
                                   class="text-green-500 hover:underline mr-3">Download</a>
                                <form method="POST" action="{{ route('papers.destroy', $paper) }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline"
                                            onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
