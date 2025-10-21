@extends('backend.layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Notifications') }}</h1>
        @if($notifications->where('read_at', null)->count() > 0)
            <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" class="inline">
                @csrf
                <button type="submit" class="btn btn-secondary">
                    {{ __('Mark All as Read') }}
                </button>
            </form>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        @forelse($notifications as $notification)
            <div class="border-b border-gray-200 dark:border-gray-700 last:border-b-0 {{ $notification->read_at ? 'opacity-60' : '' }}">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                @if($notification->type === 'news_created')
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <iconify-icon icon="lucide:newspaper" class="text-blue-600 dark:text-blue-400" width="20" height="20"></iconify-icon>
                                    </div>
                                @elseif($notification->type === 'news_status_changed')
                                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900 rounded-full flex items-center justify-center">
                                        <iconify-icon icon="lucide:edit" class="text-orange-600 dark:text-orange-400" width="20" height="20"></iconify-icon>
                                    </div>
                                @else
                                    <div class="w-10 h-10 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                        <iconify-icon icon="lucide:bell" class="text-gray-600 dark:text-gray-400" width="20" height="20"></iconify-icon>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $notification->title }}
                                </h3>
                                <p class="mt-1 text-gray-600 dark:text-gray-400">
                                    {{ $notification->message }}
                                </p>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-500">
                                    {{ $notification->created_at->format('M j, Y \a\t g:i A') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if(!$notification->read_at)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    {{ __('New') }}
                                </span>
                                <form method="POST" action="{{ route('admin.notifications.read', $notification) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <iconify-icon icon="lucide:check" width="16" height="16"></iconify-icon>
                                    </button>
                                </form>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    {{ __('Read') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <iconify-icon icon="lucide:bell-off" class="mx-auto h-12 w-12 text-gray-400" width="48" height="48"></iconify-icon>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">{{ __('No notifications') }}</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('You have no notifications at this time.') }}</p>
            </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @endif
</div>
@endsection