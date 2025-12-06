<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div class="max-w-4xl mx-auto">
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-700 p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $contactMessage->subject }}</h1>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Received') }} {{ $contactMessage->created_at->format('M j, Y \a\t g:i A') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @php
                            $statusClasses = [
                                'new' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
                                'read' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                'replied' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
                            ];
                            $statusLabels = [
                                'new' => __('New'),
                                'read' => __('Read'),
                                'replied' => __('Replied'),
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusClasses[$contactMessage->status] ?? $statusClasses['read'] }}">
                            {{ $statusLabels[$contactMessage->status] ?? ucfirst($contactMessage->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Sender Info -->
            <div class="border-b border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">{{ __('Sender Information') }}</h2>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Name') }}</p>
                        <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $contactMessage->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Email') }}</p>
                        <p class="mt-1 text-base font-medium text-gray-900 dark:text-white">
                            <a href="mailto:{{ $contactMessage->email }}" class="text-blue-600 hover:underline dark:text-blue-400">
                                {{ $contactMessage->email }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Message Content -->
            <div class="p-6">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">{{ __('Message') }}</h2>
                <div class="prose prose-sm dark:prose-invert max-w-none">
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $contactMessage->message }}</p>
                </div>
            </div>

            <!-- Meta Info -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-6 bg-gray-50 dark:bg-gray-900/50 rounded-b-lg">
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-4">{{ __('Additional Information') }}</h2>
                @php
                    $parsedUA = $contactMessage->parsed_user_agent;
                @endphp
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('IP Address') }}</p>
                        <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $contactMessage->ip_address ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Browser') }}</p>
                        <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $parsedUA['browser'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Operating System') }}</p>
                        <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $parsedUA['os'] }}</p>
                    </div>
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Device') }}</p>
                        <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $parsedUA['device'] }}</p>
                    </div>
                    @if($contactMessage->read_at)
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Read at') }}</p>
                        <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $contactMessage->read_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @endif
                    @if($contactMessage->replied_at)
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('Replied at') }}</p>
                        <p class="mt-1 text-gray-700 dark:text-gray-300">{{ $contactMessage->replied_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <a href="{{ route('admin.contact-messages.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                <iconify-icon icon="lucide:arrow-left" width="16" height="16"></iconify-icon>
                {{ __('Back to messages') }}
            </a>
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.contact-messages.toggle-replied', $contactMessage) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    @if($contactMessage->status === 'replied')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        <iconify-icon icon="lucide:x" width="16" height="16"></iconify-icon>
                        {{ __('Mark as Not Replied') }}
                    </button>
                    @else
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                        <iconify-icon icon="lucide:check" width="16" height="16"></iconify-icon>
                        {{ __('Mark as Replied') }}
                    </button>
                    @endif
                </form>
                <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ urlencode($contactMessage->subject) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <iconify-icon icon="lucide:mail" width="16" height="16"></iconify-icon>
                    {{ __('Reply via Email') }}
                </a>
                <form action="{{ route('admin.contact-messages.destroy', $contactMessage) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this message?') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                        <iconify-icon icon="lucide:trash-2" width="16" height="16"></iconify-icon>
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
