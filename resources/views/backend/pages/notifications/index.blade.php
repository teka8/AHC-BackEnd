<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="flex justify-between items-center mb-6">
        {{-- <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">{{ __('Notifications') }}</h1> --}}
        @if(auth()->user()->unreadNotifications->count() > 0)
            <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}">
                @csrf
                <button type="submit" class="btn-primary">
                    <iconify-icon icon="lucide:check-circle" class="w-4 h-4 mr-2"></iconify-icon>
                    {{ __('Mark All as Read') }}
                </button>
            </form>
        @endif
    </div>

    <x-card>
        @if($notifications->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($notifications as $notification)
                    <div class="p-4 {{ $notification->read_at ? 'bg-gray-50 dark:bg-gray-800' : 'bg-blue-50 dark:bg-blue-900/20' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    @if(!$notification->read_at)
                                        <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                    @endif
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $notification->data['message'] ?? 'Notification' }}
                                    </h3>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                @if(isset($notification->data['news_id']) && isset($notification->data['post_type']))
                                    <a href="{{ route('admin.posts.edit', ['postType' => $notification->data['post_type'], 'post' => $notification->data['news_id']]) }}" 
                                       class="text-blue-600 hover:text-blue-500 text-sm">
                                        {{ __('View') }}
                                    </a>
                                @elseif(isset($notification->data['document_id']))
                                    <a href="{{ route('admin.document.edit', $notification->data['document_id']) }}" 
                                       class="text-blue-600 hover:text-blue-500 text-sm">
                                        {{ __('View') }}
                                    </a>
                                @endif
                                @if(!$notification->read_at)
                                    <button onclick="markAsRead('{{ $notification->id }}')" 
                                            class="text-gray-500 hover:text-gray-700 text-sm">
                                        {{ __('Mark as Read') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <iconify-icon icon="lucide:bell-off" class="w-12 h-12 text-gray-400 mx-auto mb-4"></iconify-icon>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ __('No notifications') }}</h3>
                <p class="text-gray-500 dark:text-gray-400">{{ __('You have no notifications at this time.') }}</p>
            </div>
        @endif
    </x-card>
</x-layouts.backend-layout>

<script>
function markAsRead(notificationId) {
    fetch(`{{ url('admin/notifications/read') }}/${notificationId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    }).then(response => {
        if (response.ok) {
            location.reload();
        }
    });
}
</script>