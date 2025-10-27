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


<div class="relative" x-data="{ notificationOpen: false }" @click.outside="notificationOpen = false">
    <!-- Notification Button -->
    <button 
        @click="notificationOpen = !notificationOpen"
        class="relative flex items-center justify-center p-2 rounded-full text-gray-700 hover:bg-gray-100 hover:text-gray-800 transition dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white"
    >
        <iconify-icon icon="lucide:bell" width="22" height="22"></iconify-icon>
        @if(auth()->user()->unreadNotifications->count() > 0)
            <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-[10px] font-semibold rounded-full h-5 w-5 flex items-center justify-center shadow">
                {{ auth()->user()->unreadNotifications->count() }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div 
        x-show="notificationOpen" 
        x-transition:enter="transition ease-out duration-200" 
        x-transition:enter-start="opacity-0 scale-95" 
        x-transition:enter-end="opacity-100 scale-100" 
        x-transition:leave="transition ease-in duration-150" 
        x-transition:leave-start="opacity-100 scale-100" 
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden"
    >
        <!-- Header -->
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
            <div class="flex items-center gap-2">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Notifications') }}</h3>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="bg-red-500 text-white text-[10px] font-semibold rounded-full px-2 py-0.5">
                        {{ auth()->user()->unreadNotifications->count() }}
                    </span>
                @endif
            </div>
            <a href="{{ route('admin.notifications.index') }}" class="text-xs text-blue-600 hover:text-blue-500 font-medium">
                {{ __('View All') }}
            </a>
        </div>

        <!-- Notifications List -->
        @php
            $notifications = auth()->user()->unreadNotifications;
            $hasMany = $notifications->count() > 4;
        @endphp

        <div class="{{ $hasMany ? 'max-h-80 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-transparent' : '' }}">
            @forelse($notifications->take(10) as $notification)
                <a href="#"
                    @if(isset($notification->data['news_id']) && isset($notification->data['post_type']))
                        onclick="markAsReadAndRedirect('{{ $notification->id }}', '{{ route('admin.posts.edit', ['postType' => $notification->data['post_type'], 'post' => $notification->data['news_id']]) }}')"
                    @elseif(isset($notification->data['document_id']))
                        onclick="markAsReadAndRedirect('{{ $notification->id }}', '{{ route('admin.document.edit', $notification->data['document_id']) }}')"
                    @endif
                    class="flex gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150 last:border-b-0"
                >
                    <div class="flex-shrink-0 mt-1.5">
                        <div class="h-2 w-2 bg-blue-500 rounded-full shadow-sm"></div>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-900 dark:text-gray-100 leading-snug" title="{{ $notification->data['message'] }}">
                               {{ Str::limit($notification->data['message'], 35, '...') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ $notification->created_at->diffForHumans() }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="flex flex-col items-center justify-center px-4 py-6 text-center text-gray-500 dark:text-gray-400">
                    <iconify-icon icon="lucide:bell-off" class="w-8 h-8 mb-2 opacity-60"></iconify-icon>
                    <p class="text-sm">{{ __('No new notifications') }}</p>
                </div>
            @endforelse
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
            }).then(() => window.location.href = redirectUrl)
              .catch(() => window.location.href = redirectUrl);
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
