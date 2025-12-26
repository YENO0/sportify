@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-blue-600 text-white p-4 rounded-t-lg flex items-center">
        <a href="{{ route('facilities.index') }}" class="text-white mr-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h17" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold">Notifications</h1>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-b-lg">
        @if($notifications->isEmpty())
            <div class="p-6 text-center text-gray-500">
                <p class="text-lg">Not have notifications yet.</p>
            </div>
        @else
            <ul class="divide-y divide-gray-200">
                @foreach($notifications as $notification)
                    <li class="{{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                        <a href="{{ route('notifications.show', $notification->id) }}" class="block hover:bg-gray-50">
                            <div class="px-4 py-4 sm:px-6">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium {{ $notification->read_at ? 'text-gray-600' : 'text-blue-800 font-bold' }}">
                                        {{ $notification->data['title'] ?? 'Reapply your event' }}
                                    </p>
                                    <div class="ml-2 flex-shrink-0 flex">
                                        <p class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="mt-2 sm:flex justify-between">
                                    <div class="sm:flex">
                                        <p class="flex items-center text-sm text-gray-500">
                                            Sender: {{ $notification->data['sender'] ?? 'System' }}
                                        </p>
                                    </div>
                                    @unless($notification->read_at)
                                        <div class="flex items-center text-sm text-blue-600">
                                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                            Unread
                                        </div>
                                    @endunless
                                </div>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
@endsection