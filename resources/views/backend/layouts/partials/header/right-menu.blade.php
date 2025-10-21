{!! Hook::applyFilters(AdminFilterHook::HEADER_RIGHT_MENU_BEFORE, '') !!}

<div class="flex items-center gap-1">
    <div class="hidden md:block">
        @include('backend.layouts.partials.demo-mode-notice')
    </div>

    <div class="relative" x-data="{ notificationOpen: false }" @click.outside="notificationOpen = false">
        <x-tooltip title="{{ __('Notification') }}" position="bottom">
            <button @click.prevent="notificationOpen = !notificationOpen"
                class="hover:text-dark-900 relative flex p-2 items-center justify-center rounded-full text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">
                <iconify-icon icon="lucide:bell" width="22" height="22"></iconify-icon>
@php
                    try {
                        $notificationService = app(\App\Services\NotificationService::class);
                        $unreadCount = $notificationService->getUnreadCount(auth()->user());
                    } catch (\Exception $e) {
                        $unreadCount = 0;
                        \Log::error('Notification service error: ' . $e->getMessage());
                    }
                @endphp
                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                @endif
            </button>
        </x-tooltip>
        
        <div x-show="notificationOpen" x-transition
            class="absolute right-0 top-full mt-2 w-[300px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-xl z-[99999]">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Notifications') }}</h3>
            </div>
            <div class="max-h-96 overflow-y-auto">
                @php
                    $notificationService = app(\App\Services\NotificationService::class);
                    $notifications = $notificationService->getUnreadNotifications(auth()->user());
                @endphp
                @forelse($notifications as $notification)
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                @if($notification->type === 'news_created')
                                    <iconify-icon icon="lucide:newspaper" class="text-blue-500" width="20" height="20"></iconify-icon>
                                @elseif($notification->type === 'news_status_changed')
                                    <iconify-icon icon="lucide:edit" class="text-orange-500" width="20" height="20"></iconify-icon>
                                @else
                                    <iconify-icon icon="lucide:bell" class="text-gray-500" width="20" height="20"></iconify-icon>
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $notification->title }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $notification->message }}</p>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-4 text-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No new notifications') }}</p>
                    </div>
                @endforelse
            </div>
            <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('admin.notifications.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">{{ __('View all notifications') }}</a>
            </div>
        </div>
    </div>

    {!! Hook::applyFilters(AdminFilterHook::HEADER_BEFORE_LOCALE_SWITCHER, '') !!}
    <x-tooltip title="{{ __('Change locale') }}" position="bottom">
        @include('backend.layouts.partials.locale-switcher')
    </x-tooltip>
    {!! Hook::applyFilters(AdminFilterHook::HEADER_AFTER_LOCALE_SWITCHER, '') !!}

    {!! Hook::applyFilters(AdminFilterHook::DARK_MODE_TOGGLER_BEFORE_BUTTON, '') !!}

    <x-tooltip title="{{ __('Toggle theme mode') }}" position="bottom">
        <button id="darkModeToggle"
            class="hover:text-dark-900 relative flex items-center justify-center rounded-full text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white p-2 dark-mode-toggle"
            @click.prevent="darkMode = !darkMode" @click="menuToggle = true">
            <iconify-icon icon="lucide:moon" width="24" height="24" class="hidden dark:block"></iconify-icon>
            <iconify-icon icon="lucide:sun" width="24" height="24" class="dark:hidden"></iconify-icon>
        </button>
    </x-tooltip>
    {!! Hook::applyFilters(AdminFilterHook::DARK_MODE_TOGGLER_AFTER_BUTTON, '') !!}

    @if (config('app.show_demo_component_preview', false))
        <x-tooltip title="{{ __('Preview demo components') }}" position="bottom">
            <a href="{{ route('demo.preview') }}" class="hover:text-dark-900 relative flex p-2 items-center justify-center rounded-full text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">
                <iconify-icon icon="lucide:view" width="22" height="22"></iconify-icon>
            </a>
        </x-tooltip>
    @endif

    {!! Hook::applyFilters(AdminFilterHook::HEADER_AFTER_ACTIONS, '') !!}
</div>

{!! Hook::applyFilters(AdminFilterHook::USER_DROPDOWN_BEFORE, '') !!}

<div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
    <a class="flex items-center text-gray-700 dark:text-gray-300" href="#"
        @click.prevent="dropdownOpen = ! dropdownOpen">
        <span class="mr-3 h-8 w-8 overflow-hidden rounded-full">
            <img src="{{ auth()->user()->avatar_url ? auth()->user()->avatar_url : auth()->user()->getGravatarUrl() }}" alt="User" />
        </span>
    </a>

    <div x-show="dropdownOpen"
        class="absolute right-0 mt-[17px] flex w-[220px] flex-col rounded-md border bg-white dark:bg-gray-700 border-gray-200  p-3 shadow-theme-lg dark:border-gray-800 z-100"
        style="display: none">
        <div class="border-b border-gray-200 pb-2 dark:border-gray-800 mb-2">
            <span class="block font-medium text-gray-700 dark:text-gray-300">
                {{ auth()->user()->full_name }}
            </span>
            <span class="mt-0.5 block text-theme-sm text-gray-700 dark:text-gray-300">
                {{ auth()->user()->email }}
            </span>
        </div>

        {!! Hook::applyFilters(AdminFilterHook::USER_DROPDOWN_AFTER_USER_INFO, '') !!}

        <ul class="flex flex-col gap-1 border-b border-gray-200 pb-2 dark:border-gray-800">
            <li>
                <a href="{{ route('profile.edit') }}"
                    class="group flex items-center gap-3 rounded-md px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-800 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-gray-300">
                    <iconify-icon icon="lucide:user" width="20" height="20" class="fill-gray-500 group-hover:fill-gray-700 dark:fill-gray-400 dark:group-hover:fill-gray-300"></iconify-icon>
                    {{ __('Edit profile') }}
                </a>
            </li>
        </ul>
        {!! Hook::applyFilters(AdminFilterHook::USER_DROPDOWN_AFTER_PROFILE_LINKS, '') !!}

        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit"
                class="group flex items-center gap-3 rounded-md px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-800 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-gray-300 mt-2 w-full">
                <iconify-icon icon="lucide:log-out" width="20" height="20" class="fill-gray-500 group-hover:fill-gray-700 dark:group-hover:fill-gray-300"></iconify-icon>
                {{ __('Logout') }}
            </button>
        </form>

        {!! Hook::applyFilters(AdminFilterHook::USER_DROPDOWN_AFTER_LOGOUT, '') !!}

        @if (session()->has('original_user_id'))
            @php
                $originalUser = \App\Models\User::find(session('original_user_id'));
            @endphp
            @if ($originalUser)
                <form method="POST" action="{{ route('admin.users.switch-back') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="group flex items-center gap-3 rounded-md px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-800 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-gray-300 mt-1 w-full">
                        <iconify-icon icon="lucide:arrow-left" width="16" height="16"></iconify-icon>
                        {{ __('Switch back to') }} {{ $originalUser->full_name }}
                    </button>
                </form>
            @endif
        @endif
    </div>
</div>

{!! Hook::applyFilters(AdminFilterHook::HEADER_RIGHT_MENU_AFTER, '') !!}