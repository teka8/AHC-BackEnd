{!! Hook::applyFilters(AdminFilterHook::HEADER_RIGHT_MENU_BEFORE, '') !!}

<div class="flex items-center gap-1">
    <div class="hidden md:block">
        @include('backend.layouts.partials.demo-mode-notice')
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
            <a href="{{ route('demo.preview') }}"
                class="hover:text-dark-900 relative flex p-2 items-center justify-center rounded-full text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">
                <iconify-icon icon="lucide:view" width="22" height="22"></iconify-icon>
            </a>
        </x-tooltip>
    @endif

    <!-- Notifications -->
    <div class="relative" x-data="{ notificationOpen: false }" @click.outside="notificationOpen = false">
            <button @click="notificationOpen = !notificationOpen"
                class="hover:text-dark-900 relative flex p-2 items-center justify-center rounded-full text-gray-700 transition-colors hover:bg-gray-100 hover:text-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">
                <iconify-icon icon="lucide:bell" width="22" height="22"></iconify-icon>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </button>
        
        <div x-show="notificationOpen" x-transition
            class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 shadow-lg rounded-md border border-gray-200 dark:border-gray-700 z-50">
            <div class="p-3 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ __('Notifications') }}</h3>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="bg-red-500 text-white text-xs rounded-full px-2 py-0.5">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('admin.notifications.index') }}" class="text-xs text-blue-600 hover:text-blue-500">
                        {{ __('View All') }}
                    </a>
                </div>
            </div>
            <div class="max-h-64 overflow-y-auto">
                @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                    <a href="#"
                        onclick="markAsReadAndRedirect('{{ $notification->id }}', '{{ route('admin.posts.edit', ['postType' => $notification->data['post_type'], 'post' => $notification->data['news_id']]) }}')"
                        class="block px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                        <div class="flex items-start gap-3">
                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white truncate">
                                    {{ $notification->data['message'] }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="px-4 py-6 text-center">
                        <iconify-icon icon="lucide:bell-off" class="w-8 h-8 text-gray-400 mx-auto mb-2"></iconify-icon>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No new notifications') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        function markAsReadAndRedirect(notificationId, redirectUrl) {
            fetch(`{{ url('admin/notifications/read') }}/${notificationId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(response => {
                if (response.ok) {
                    window.location.href = redirectUrl;
                } else {
                    console.error('Failed to mark as read');
                    window.location.href = redirectUrl;
                }
            }).catch(error => {
                console.error('Error:', error);
                window.location.href = redirectUrl;
            });
        }
    </script>


    {!! Hook::applyFilters(AdminFilterHook::HEADER_AFTER_ACTIONS, '') !!}
</div>

{!! Hook::applyFilters(AdminFilterHook::USER_DROPDOWN_BEFORE, '') !!}

<div class="relative" x-data="{ dropdownOpen: false }" @click.outside="dropdownOpen = false">
    <a class="flex items-center text-gray-700 dark:text-gray-300" href="#"
        @click.prevent="dropdownOpen = ! dropdownOpen">
        <span class="mr-3 h-8 w-8 overflow-hidden rounded-full">
            <img src="{{ auth()->user()->avatar_url ? auth()->user()->avatar_url : auth()->user()->getGravatarUrl() }}"
                alt="User" />
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
                    <iconify-icon icon="lucide:user" width="20" height="20"
                        class="fill-gray-500 group-hover:fill-gray-700 dark:fill-gray-400 dark:group-hover:fill-gray-300"></iconify-icon>
                    {{ __('Edit profile') }}
                </a>
            </li>
        </ul>
        {!! Hook::applyFilters(AdminFilterHook::USER_DROPDOWN_AFTER_PROFILE_LINKS, '') !!}

        <form method="POST" action="{{ route('logout') }}" class="inline">
            @csrf
            <button type="submit"
                class="group flex items-center gap-3 rounded-md px-3 py-2 text-theme-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-800 dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-gray-300 mt-2 w-full">
                <iconify-icon icon="lucide:log-out" width="20" height="20"
                    class="fill-gray-500 group-hover:fill-gray-700 dark:group-hover:fill-gray-300"></iconify-icon>
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
