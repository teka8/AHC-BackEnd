@props(['folders', 'selectedFolderId' => null, 'level' => 0])

@if ($folders->isNotEmpty())
    <ul class="space-y-1 {{ $level > 0 ? 'pl-4 border-l border-gray-200 dark:border-slate-700/60 ml-2' : '' }}">
        @foreach ($folders as $folder)
            <li>
                <div class="flex items-center justify-between rounded px-2 py-2 text-sm transition-colors {{ (int) $selectedFolderId === (int) $folder->id ? 'bg-ahc-green/10 text-ahc-green-dark dark:text-ahc-green-light' : 'hover:bg-gray-100 dark:hover:bg-slate-800/70' }}">
                    <a href="{{ route('admin.media-manager.index', ['folder' => $folder->id]) }}" class="flex items-center gap-2 flex-1">
                        <iconify-icon icon="lucide:folder" class="text-base {{ (int) $selectedFolderId === (int) $folder->id ? 'text-ahc-green-dark dark:text-ahc-green-light' : 'text-slate-500 dark:text-slate-400' }}"></iconify-icon>
                        <span class="truncate">{{ $folder->name }}</span>
                    </a>
                    <span class="text-xs font-medium text-slate-400 dark:text-slate-500 ml-2">
                        {{ $folder->media_count }}
                    </span>
                </div>

                @if ($folder->relationLoaded('children') && $folder->children->isNotEmpty())
                    @include('backend.pages.media-manager.partials.tree', [
                        'folders' => $folder->children,
                        'selectedFolderId' => $selectedFolderId,
                        'level' => $level + 1,
                    ])
                @endif
            </li>
        @endforeach
    </ul>
@else
    <p class="text-sm text-slate-500 dark:text-slate-400 italic">
        {{ __('No folders found yet.') }}
    </p>
@endif
