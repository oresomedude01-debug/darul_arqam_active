@extends('layouts.spa')

@section('title', 'Notifications')

@section('content')
<div class="max-w-4xl mx-auto p-6">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
            <p class="text-gray-600 mt-2">View all your system notifications</p>
        </div>
        @if(auth()->user()->unreadNotifications->count() > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
            @csrf
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition shadow-sm flex items-center gap-2">
                <i class="fas fa-check-double"></i>
                Mark all as read
            </button>
        </form>
        @endif
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg flex items-center gap-2">
        <i class="fas fa-check-circle"></i>
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-xl shadow-md border border-gray-100 overflow-hidden">
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
                <div class="p-4 sm:p-6 hover:bg-gray-50 transition flex items-start gap-4 {{ $notification->read_at ? 'opacity-70' : 'bg-blue-50/20' }}">
                    <div class="w-10 h-10 rounded-full {{ $notification->read_at ? 'bg-gray-100 text-gray-500' : 'bg-primary-100 text-primary-600' }} flex items-center justify-center flex-shrink-0 mt-1">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-1">
                            <h3 class="font-semibold {{ $notification->read_at ? 'text-gray-700' : 'text-gray-900' }}">
                                {{ $notification->data['title'] ?? 'Notification' }}
                            </h3>
                            <span class="text-xs text-gray-500 whitespace-nowrap bg-gray-100 px-2 py-1 rounded-md">
                                <i class="fas fa-clock mr-1"></i>{{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-gray-600 text-sm mb-3">{{ $notification->data['message'] ?? 'You have a new notification.' }}</p>
                        
                        <div class="flex gap-3">
                            @if(isset($notification->data['action_url']))
                            <a href="{{ $notification->data['action_url'] }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 transition">
                                View Details &rarr;
                            </a>
                            @endif
                            
                            @if(!$notification->read_at)
                            <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-sm font-medium text-gray-500 hover:text-gray-800 transition">
                                    Mark as read
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @if(!$notification->read_at)
                    <div class="w-2 h-2 rounded-full bg-primary-500 flex-shrink-0 mt-2"></div>
                    @endif
                </div>
            @empty
                <div class="p-12 text-center flex flex-col items-center justify-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100">
                        <i class="fas fa-bell-slash text-2xl text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">All caught up!</h3>
                    <p class="text-gray-500">You don't have any notifications right now.</p>
                </div>
            @endforelse
        </div>
    </div>

    @if($notifications->hasPages())
    <div class="mt-8">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection
