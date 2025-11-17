<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div
        x-data="mediaManagerPage({
            selectedFolderId: {{ $selectedFolderId ? (int) $selectedFolderId : 'null' }},
            uploadLimits: @js($uploadLimits),
            labels: @js([
                'unknownType' => __('Unknown'),
                'filesSelected' => __('files selected'),
                'notifyCopied' => __('URL copied to clipboard.'),
                'noCaption' => __('No caption provided.'),
            ]),
        })"
        class="space-y-6"
    >
        <x-messages />

        @if (! $selectedFolderId)
            <section class="bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-slate-800 rounded-2xl p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-800 dark:text-white">{{ __('Media Folders') }}</h1>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ __('Organise assets into folders and subfolders for effortless browsing.') }}
                        </p>
                    </div>

                    @can('media.create')
                        <button
                            type="button"
                            class="btn-primary flex items-center gap-2"
                            @click.prevent="openFolderModal('create', {
                                action: '{{ route('admin.media-manager.folders.store') }}',
                                title: '{{ __('Create folder') }}',
                                submit: '{{ __('Create folder') }}',
                                parent_id: null,
                            })"
                        >
                            <iconify-icon icon="lucide:folder-plus" class="w-4 h-4"></iconify-icon>
                            {{ __('Add Folder') }}
                        </button>
                    @endcan
                </div>

                @if ($flatFolders->isEmpty())
                    <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/40 p-12 text-center space-y-4">
                        <iconify-icon icon="lucide:folder" class="mx-auto text-5xl text-slate-300 dark:text-slate-600"></iconify-icon>
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ __('No folders yet. Create one to start uploading media.') }}
                        </p>
                        @can('media.create')
                            <button
                                type="button"
                                class="btn-primary"
                                @click.prevent="openFolderModal('create', {
                                    action: '{{ route('admin.media-manager.folders.store') }}',
                                    title: '{{ __('Create folder') }}',
                                    submit: '{{ __('Create folder') }}',
                                    parent_id: null,
                                })"
                            >
                                {{ __('Create Folder') }}
                            </button>
                        @endcan
                    </div>
                @else
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($flatFolders as $item)
                            @php($folder = $item['folder'])

                            <div
                                x-data="{ hovered: false }"
                                @mouseenter="hovered = true"
                                @mouseleave="hovered = false"
                                class="relative group rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900/40 overflow-hidden shadow-sm"
                            >
                                <a
                                    href="{{ route('admin.media-manager.index', ['folder' => $folder->getKey()]) }}"
                                    class="block p-6 transition-colors duration-200 group-hover:bg-primary/5"
                                >
                                    <div class="flex items-start gap-4">
                                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                            <iconify-icon icon="lucide:folder" class="w-6 h-6"></iconify-icon>
                                        </span>
                                        <div class="min-w-0 space-y-1">
                                            <p
                                                class="text-base font-semibold text-slate-800 dark:text-white truncate"
                                                style="padding-left: {{ $item['depth'] * 0.75 }}rem"
                                            >
                                                {{ $folder->name }}
                                            </p>
                                            <p class="text-xs uppercase tracking-wide text-slate-400 dark:text-slate-500">
                                                {{ trans_choice(':count item|:count items', $folder->media_count, ['count' => $folder->media_count]) }}
                                            </p>
                                        </div>
                                    </div>
                                </a>

                                <div class="absolute inset-x-0 bottom-0 transition-opacity duration-200" :class="hovered ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'">
                                    <div class="flex items-center justify-between gap-2 border-t border-white/60 dark:border-slate-800 bg-white/95 dark:bg-slate-950/90 px-4 py-3 rounded-t-xl">
                                        <div class="flex items-center gap-2">
                                            @can('media.edit')
                                                <button
                                                    type="button"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white/95 text-slate-600 transition hover:border-primary hover:text-primary pointer-events-auto"
                                                    title="{{ __('Rename folder') }}"
                                                    @click.prevent="openFolderModal('edit', {
                                                        action: '{{ route('admin.media-manager.folders.update', $folder) }}',
                                                        title: '{{ __('Rename folder') }}',
                                                        submit: '{{ __('Save changes') }}',
                                                        name: @js($folder->name),
                                                        description: @js($folder->description),
                                                        parent_id: {{ $folder->parent_id ? (int) $folder->parent_id : 'null' }},
                                                    })"
                                                >
                                                    <iconify-icon icon="lucide:square-pen" class="w-4 h-4"></iconify-icon>
                                                    <span class="sr-only">{{ __('Rename folder') }}</span>
                                                </button>
                                            @endcan

                                            @can('media.create')
                                                <button
                                                    type="button"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-200 bg-white/95 text-slate-600 transition hover:border-primary hover:text-primary pointer-events-auto"
                                                    title="{{ __('Add subfolder') }}"
                                                    @click.prevent="openFolderModal('create', {
                                                        action: '{{ route('admin.media-manager.folders.store') }}',
                                                        title: '{{ __('Add subfolder') }}',
                                                        submit: '{{ __('Create folder') }}',
                                                        parent_id: {{ $folder->getKey() }},
                                                    })"
                                                >
                                                    <iconify-icon icon="lucide:folder-plus" class="w-4 h-4"></iconify-icon>
                                                    <span class="sr-only">{{ __('Add subfolder') }}</span>
                                                </button>
                                            @endcan

                                            @can('media.delete')
                                                <button
                                                    type="button"
                                                    class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-red-200 bg-white/95 text-red-500 transition hover:border-red-500 hover:text-red-600 pointer-events-auto"
                                                    title="{{ __('Delete folder') }}"
                                                    @click.prevent="openFolderDeleteModal({
                                                        name: @js($folder->name),
                                                        action: '{{ route('admin.media-manager.folders.destroy', $folder) }}'
                                                    })"
                                                >
                                                    <iconify-icon icon="lucide:trash-2" class="w-4 h-4"></iconify-icon>
                                                    <span class="sr-only">{{ __('Delete folder') }}</span>
                                                </button>
                                            @endcan
                                        </div>

                                        <a
                                            href="{{ route('admin.media-manager.index', ['folder' => $folder->getKey()]) }}"
                                            class="btn-primary btn-sm flex items-center gap-2"
                                        >
                                            <iconify-icon icon="lucide:arrow-right" class="w-4 h-4"></iconify-icon>
                                            {{ __('Open') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        @elseif ($folderData)
            @php($folder = $folderData['folder'])

            <section class="space-y-6">
                <div class="bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-slate-800 rounded-2xl p-6 space-y-5">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <a href="{{ route('admin.media-manager.index') }}" class="btn-secondary btn-icon">
                                <iconify-icon icon="lucide:arrow-left" class="w-5 h-5"></iconify-icon>
                            </a>
                            <div>
                                <h1 class="text-2xl font-semibold text-slate-800 dark:text-white">{{ $folder->name }}</h1>
                                @if ($folder->description)
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ $folder->description }}</p>
                                @endif
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            @can('media.create')
                                <button
                                    type="button"
                                    class="btn-secondary flex items-center gap-2"
                                    @click.prevent="openFolderModal('create', {
                                        action: '{{ route('admin.media-manager.folders.store') }}',
                                        title: '{{ __('Add subfolder') }}',
                                        submit: '{{ __('Create folder') }}',
                                        parent_id: {{ $folder->getKey() }},
                                    })"
                                >
                                    <iconify-icon icon="lucide:folder-plus" class="w-4 h-4"></iconify-icon>
                                    {{ __('Add subfolder') }}
                                </button>

                                <button
                                    type="button"
                                    class="btn-primary flex items-center gap-2"
                                    @click.prevent="openUploadModal({
                                        action: '{{ route('admin.media-manager.folders.upload', $folder) }}',
                                        name: @js($folder->name)
                                    })"
                                >
                                    <iconify-icon icon="lucide:upload" class="w-4 h-4"></iconify-icon>
                                    {{ __('Upload media') }}
                                </button>
                            @endcan

                            @can('media.edit')
                                <button
                                    type="button"
                                    class="btn-ghost-secondary btn-icon"
                                    title="{{ __('Rename folder') }}"
                                    @click.prevent="openFolderModal('edit', {
                                        action: '{{ route('admin.media-manager.folders.update', $folder) }}',
                                        title: '{{ __('Rename folder') }}',
                                        submit: '{{ __('Save changes') }}',
                                        name: @js($folder->name),
                                        description: @js($folder->description),
                                        parent_id: {{ $folder->parent_id ? (int) $folder->parent_id : 'null' }},
                                    })"
                                >
                                    <iconify-icon icon="lucide:square-pen" class="w-4 h-4"></iconify-icon>
                                </button>
                            @endcan

                            @can('media.delete')
                                <button
                                    type="button"
                                    class="btn-ghost-danger btn-icon"
                                    title="{{ __('Delete folder') }}"
                                    @click.prevent="openFolderDeleteModal({
                                        name: @js($folder->name),
                                        action: '{{ route('admin.media-manager.folders.destroy', $folder) }}'
                                    })"
                                >
                                    <iconify-icon icon="lucide:trash-2" class="w-4 h-4"></iconify-icon>
                                </button>
                            @endcan
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 dark:text-slate-300">
                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1">
                            <iconify-icon icon="lucide:layers" class="w-3.5 h-3.5 text-slate-400"></iconify-icon>
                            {{ __('Items') }}: {{ $folderData['media']->total() }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1">
                            <iconify-icon icon="lucide:image" class="w-3.5 h-3.5 text-slate-400"></iconify-icon>
                            {{ $folderData['stats']['images'] }} {{ __('images') }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1">
                            <iconify-icon icon="lucide:video" class="w-3.5 h-3.5 text-slate-400"></iconify-icon>
                            {{ $folderData['stats']['videos'] }} {{ __('videos') }}
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 dark:bg-slate-800 px-3 py-1">
                            <iconify-icon icon="lucide:hard-drive" class="w-3.5 h-3.5 text-slate-400"></iconify-icon>
                            {{ $folderData['stats']['total_size'] }}
                        </span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @php($type = request('type'))
                        <a
                            href="{{ route('admin.media-manager.index', array_merge(request()->except(['type', 'page']), ['folder' => $folder->getKey()])) }}"
                            class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $type ? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' : 'bg-primary/10 text-primary dark:bg-primary/20' }}"
                        >
                            <iconify-icon icon="lucide:layers" class="w-4 h-4"></iconify-icon>
                            {{ __('All') }}
                        </a>
                        <a
                            href="{{ route('admin.media-manager.index', array_merge(request()->except(['type', 'page']), ['folder' => $folder->getKey(), 'type' => 'images'])) }}"
                            class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $type === 'images' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}"
                        >
                            <iconify-icon icon="lucide:image" class="w-4 h-4"></iconify-icon>
                            {{ __('Images') }}
                        </a>
                        <a
                            href="{{ route('admin.media-manager.index', array_merge(request()->except(['type', 'page']), ['folder' => $folder->getKey(), 'type' => 'videos'])) }}"
                            class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $type === 'videos' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}"
                        >
                            <iconify-icon icon="lucide:video" class="w-4 h-4"></iconify-icon>
                            {{ __('Videos') }}
                        </a>
                        <a
                            href="{{ route('admin.media-manager.index', array_merge(request()->except(['type', 'page']), ['folder' => $folder->getKey(), 'type' => 'audio'])) }}"
                            class="inline-flex items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $type === 'audio' ? 'bg-primary/10 text-primary dark:bg-primary/20' : 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-300' }}"
                        >
                            <iconify-icon icon="lucide:music" class="w-4 h-4"></iconify-icon>
                            {{ __('Audio') }}
                        </a>
                    </div>
                </div>

                <div class="bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-slate-800 rounded-2xl p-6">
                    @if ($folderData['media']->isEmpty())
                        <div class="text-center py-16 space-y-4">
                            <iconify-icon icon="lucide:images" class="mx-auto text-5xl text-slate-300 dark:text-slate-600"></iconify-icon>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                {{ __('No media in this folder yet. Upload files to populate the gallery.') }}
                            </p>
                            @can('media.create')
                                <button
                                    type="button"
                                    class="btn-primary"
                                    @click.prevent="openUploadModal({
                                        action: '{{ route('admin.media-manager.folders.upload', $folder) }}',
                                        name: @js($folder->name)
                                    })"
                                >
                                    {{ __('Upload media') }}
                                </button>
                            @endcan
                        </div>
                    @else
                        <div class="grid gap-5 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($folderData['media'] as $item)
                                @php($extension = strtoupper(pathinfo($item->file_name, PATHINFO_EXTENSION)))

                                <div
                                    x-data="{ hovered: false }"
                                    @mouseenter="hovered = true"
                                    @mouseleave="hovered = false"
                                    class="relative group rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm"
                                >
                                    <div class="aspect-video bg-slate-100 dark:bg-slate-900 overflow-hidden">
                                        @if ($item->type === 'image')
                                            <img
                                                src="{{ $item->thumb_url }}"
                                                alt="{{ $item->file_name }}"
                                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                            >
                                        @elseif ($item->type === 'video')
                                            <video class="h-full w-full object-cover" src="{{ $item->thumb_url ?? $item->url }}" muted></video>
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-slate-400">
                                                <iconify-icon icon="lucide:file" class="w-12 h-12"></iconify-icon>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-4 space-y-2">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0 space-y-1">
                                                <p class="text-sm font-semibold text-slate-800 dark:text-white truncate" title="{{ $item->file_name }}">
                                                    {{ $item->name ?? $item->file_name }}
                                                </p>
                                                <p class="text-xs uppercase text-slate-400 dark:text-slate-500">
                                                    {{ $extension }} • {{ $item->human_readable_size }}
                                                </p>
                                            </div>
                                            <p class="text-xs text-slate-400 dark:text-slate-500 shrink-0">
                                                {{ $item->created_at?->diffForHumans() }}
                                            </p>
                                        </div>

                                        @if ($item->caption)
                                            <p class="text-sm text-slate-600 dark:text-slate-300 line-clamp-3">{{ $item->caption }}</p>
                                        @else
                                            <p class="text-xs italic text-slate-400">{{ __('No caption provided.') }}</p>
                                        @endif
                                    </div>

                                    <div class="absolute inset-0 flex flex-col justify-between bg-gradient-to-b from-transparent via-black/10 to-black/60 transition-opacity duration-200" :class="hovered ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'">
                                        <div class="flex justify-end gap-2 p-4">
                                            <a
                                                href="{{ $item->url }}"
                                                target="_blank"
                                                rel="noopener"
                                                class="btn-secondary btn-icon"
                                                title="{{ __('Open in new tab') }}"
                                            >
                                                <iconify-icon icon="lucide:external-link" class="w-4 h-4"></iconify-icon>
                                            </a>
                                            <button
                                                type="button"
                                                class="btn-secondary btn-icon"
                                                title="{{ __('Copy URL') }}"
                                                @click.prevent="copyMediaUrl('{{ $item->url }}')"
                                            >
                                                <iconify-icon icon="lucide:copy" class="w-4 h-4"></iconify-icon>
                                            </button>
                                        </div>

                                        <div class="flex items-center justify-between gap-2 p-4">
                                            <div class="flex items-center gap-2">
                                                @can('media.edit')
                                                    <button
                                                        type="button"
                                                        class="btn-primary btn-sm shadow-lg"
                                                        @click.prevent="openMediaEditModal({
                                                            action: '{{ route('admin.media-manager.media.update', $item) }}',
                                                            caption: @js($item->caption),
                                                            name: @js($item->name ?? $item->file_name),
                                                        })"
                                                    >
                                                        <iconify-icon icon="lucide:edit-3" class="w-4 h-4"></iconify-icon>
                                                        <span>{{ __('Edit') }}</span>
                                                    </button>
                                                @endcan

                                                @can('media.delete')
                                                    <button
                                                        type="button"
                                                        class="btn-danger btn-sm shadow-lg"
                                                        @click.prevent="openMediaDeleteModal({
                                                            action: '{{ route('admin.media-manager.media.destroy', $item) }}',
                                                            name: @js($item->name ?? $item->file_name)
                                                        })"
                                                    >
                                                        <iconify-icon icon="lucide:trash-2" class="w-4 h-4"></iconify-icon>
                                                        <span>{{ __('Delete') }}</span>
                                                    </button>
                                                @endcan
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $folderData['media']->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </section>
        @else
            <section class="bg-white dark:bg-white/[0.03] border border-gray-200 dark:border-slate-800 rounded-2xl p-10 text-center space-y-4">
                <iconify-icon icon="lucide:alert-circle" class="mx-auto text-4xl text-amber-400"></iconify-icon>
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('The selected folder could not be found.') }}</p>
                <a href="{{ route('admin.media-manager.index') }}" class="btn-primary inline-flex items-center gap-2">
                    <iconify-icon icon="lucide:arrow-left" class="w-4 h-4"></iconify-icon>
                    {{ __('Back to folders') }}
                </a>
            </section>
        @endif

        <template x-teleport="body">
            <div>
                <!-- Folder create/edit modal -->
                <div
                    x-cloak
                    x-show="folderModal.open"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                    @keydown.escape.window="closeModal('folderModal')"
                >
                    <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-slate-950 shadow-xl border border-white/20 dark:border-slate-800">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-6 py-4">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-white" x-text="folderModal.title"></h2>
                            <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" @click="closeModal('folderModal')">
                                <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
                            </button>
                        </div>

                        <form method="POST" :action="folderModal.action" class="space-y-4 px-6 py-6">
                            @csrf
                            <template x-if="folderModal.mode === 'edit'">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div class="space-y-1">
                                <label for="folder-name" class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('Folder name') }}</label>
                                <input
                                    id="folder-name"
                                    type="text"
                                    name="name"
                                    class="form-input"
                                    required
                                    x-model="folderModal.name"
                                >
                            </div>

                            <div class="space-y-1">
                                <label for="folder-description" class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('Description (optional)') }}</label>
                                <textarea
                                    id="folder-description"
                                    name="description"
                                    rows="3"
                                    class="form-textarea"
                                    x-model="folderModal.description"
                                ></textarea>
                            </div>

                            <div class="space-y-1">
                                <label for="folder-parent" class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('Parent folder') }}</label>
                                <select id="folder-parent" name="parent_id" class="form-select" x-model="folderModal.parent_id">
                                    <option value="">{{ __('None (root)') }}</option>
                                    @foreach ($flatFolders as $item)
                                        <option value="{{ $item['id'] }}">
                                            {{ str_repeat('— ', $item['depth']) . $item['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex justify-end gap-2 pt-4">
                                <button type="button" class="btn-secondary" @click="closeModal('folderModal')">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn-primary" x-text="folderModal.submit"></button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Folder delete modal -->
                <div
                    x-cloak
                    x-show="folderDeleteModal.open"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                    @keydown.escape.window="closeModal('folderDeleteModal')"
                >
                    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-950 shadow-xl border border-white/20 dark:border-slate-800">
                        <div class="px-6 py-6 space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-500">
                                    <iconify-icon icon="lucide:shield-alert" class="w-6 h-6"></iconify-icon>
                                </span>
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-800 dark:text-white">{{ __('Delete folder') }}</h2>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">
                                        {{ __('Deleting a folder removes it permanently. Make sure it is empty first.') }}
                                    </p>
                                </div>
                            </div>

                            <p class="text-sm text-slate-600 dark:text-slate-300">
                                {{ __('You are about to delete:') }}
                                <strong class="text-slate-900 dark:text-white" x-text="folderDeleteModal.name"></strong>
                            </p>

                            <form method="POST" :action="folderDeleteModal.action" class="flex justify-end gap-2">
                                @csrf
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" class="btn-secondary" @click="closeModal('folderDeleteModal')">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn-danger">{{ __('Delete folder') }}</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Upload modal -->
                <div
                    x-cloak
                    x-show="uploadModal.open"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                    @keydown.escape.window="closeModal('uploadModal')"
                >
                    <div class="w-full max-w-3xl rounded-2xl bg-white dark:bg-slate-950 shadow-xl border border-white/20 dark:border-slate-800">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-6 py-4">
                            <div>
                                <h2 class="text-lg font-semibold text-slate-800 dark:text-white">{{ __('Upload media') }}</h2>
                                <p class="text-xs text-slate-400 dark:text-slate-500">
                                    {{ __('Uploading to') }} <span class="font-medium text-slate-700 dark:text-slate-200" x-text="uploadModal.name"></span>
                                </p>
                            </div>
                            <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" @click="closeModal('uploadModal')">
                                <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
                            </button>
                        </div>

                        <form method="POST" :action="uploadModal.action" enctype="multipart/form-data" class="px-6 py-6 space-y-6">
                            @csrf

                            <div class="rounded-xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-900/40 p-6 text-center">
                                <input
                                    type="file"
                                    name="files[]"
                                    multiple
                                    class="hidden"
                                    x-ref="uploadInput"
                                    @change="handleFileSelection"
                                >
                                <button type="button" class="btn-primary" @click="$refs.uploadInput.click()">
                                    <iconify-icon icon="lucide:cloud-upload" class="w-5 h-5 mr-2"></iconify-icon>
                                    {{ __('Select files') }}
                                </button>
                                <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                                    {{ __('You can add up to :count files per upload.', ['count' => $uploadLimits['max_file_uploads']]) }}
                                    {{ __('Server limit: :size per file (effective).', ['size' => $uploadLimits['effective_max_filesize_formatted']]) }}
                                </p>
                            </div>

                            <template x-if="uploadModal.files.length">
                                <div class="space-y-4">
                                    <p class="text-sm font-medium text-slate-700 dark:text-slate-200" x-text="uploadModal.files.length + ' ' + labels.filesSelected"></p>

                                    <template x-for="(file, index) in uploadModal.files" :key="file.id">
                                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 px-4 py-3 space-y-3">
                                            <div class="flex items-start gap-3">
                                                <div class="mt-1">
                                                    <iconify-icon icon="lucide:file" class="w-6 h-6 text-slate-400"></iconify-icon>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-slate-800 dark:text-white truncate" x-text="file.name"></p>
                                                    <p class="text-xs text-slate-400" x-text="formatFileSize(file.size) + ' • ' + (file.type || labels.unknownType)"></p>
                                                </div>
                                                <button type="button" class="btn-ghost-danger btn-icon" @click="removeUploadFile(index)">
                                                    <iconify-icon icon="lucide:x" class="w-4 h-4"></iconify-icon>
                                                </button>
                                            </div>

                                            <div class="space-y-2">
                                                <label class="text-xs font-medium text-slate-500 dark:text-slate-300">{{ __('Caption (optional)') }}</label>
                                                <input
                                                    type="text"
                                                    class="form-input text-sm"
                                                    name="captions[]"
                                                    x-model="file.caption"
                                                    maxlength="500"
                                                    placeholder="{{ __('Describe this media...') }}"
                                                >
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <div class="flex justify-end gap-2">
                                <button type="button" class="btn-secondary" @click="closeModal('uploadModal')">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn-primary" :disabled="uploadModal.files.length === 0">
                                    {{ __('Upload now') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Media edit modal -->
                <div
                    x-cloak
                    x-show="mediaEditModal.open"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                    @keydown.escape.window="closeModal('mediaEditModal')"
                >
                    <div class="w-full max-w-lg rounded-2xl bg-white dark:bg-slate-950 shadow-xl border border-white/20 dark:border-slate-800">
                        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-6 py-4">
                            <h2 class="text-lg font-semibold text-slate-800 dark:text-white">{{ __('Edit details') }}</h2>
                            <button type="button" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" @click="closeModal('mediaEditModal')">
                                <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
                            </button>
                        </div>

                        <form method="POST" :action="mediaEditModal.action" class="space-y-4 px-6 py-6">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('Media name') }}</label>
                                <input type="text" class="form-input" x-model="mediaEditModal.name" readonly>
                            </div>

                            <div class="space-y-1">
                                <label class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('Caption') }}</label>
                                <textarea name="caption" rows="4" class="form-textarea" x-model="mediaEditModal.caption" maxlength="500"></textarea>
                                <p class="text-xs text-slate-400" x-text="(mediaEditModal.caption || '').length + '/500'"></p>
                            </div>

                            <div class="flex justify-end gap-2">
                                <button type="button" class="btn-secondary" @click="closeModal('mediaEditModal')">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn-primary">{{ __('Save changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Media delete modal -->
                <div
                    x-cloak
                    x-show="mediaDeleteModal.open"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
                    @keydown.escape.window="closeModal('mediaDeleteModal')"
                >
                    <div class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-950 shadow-xl border border-white/20 dark:border-slate-800">
                        <div class="px-6 py-6 space-y-4">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-500">
                                    <iconify-icon icon="lucide:trash-2" class="w-6 h-6"></iconify-icon>
                                </span>
                                <div>
                                    <h2 class="text-lg font-semibold text-slate-800 dark:text-white">{{ __('Delete media item') }}</h2>
                                    <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('This action cannot be undone.') }}</p>
                                </div>
                            </div>

                            <p class="text-sm text-slate-600 dark:text-slate-300">
                                {{ __('You are about to delete:') }}
                                <strong class="text-slate-900 dark:text-white" x-text="mediaDeleteModal.name"></strong>
                            </p>

                            <form method="POST" :action="mediaDeleteModal.action" class="flex justify-end gap-2">
                                @csrf
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="button" class="btn-secondary" @click="closeModal('mediaDeleteModal')">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn-danger">{{ __('Delete media') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function mediaManagerPage({ selectedFolderId, uploadLimits, labels }) {
            const folderDefaults = () => ({
                open: false,
                mode: 'create',
                title: '',
                submit: '',
                action: '',
                name: '',
                description: '',
                parent_id: '',
            });

            const folderDeleteDefaults = () => ({
                open: false,
                action: '',
                name: '',
            });

            const uploadDefaults = () => ({
                open: false,
                action: '',
                name: '',
                files: [],
            });

            const mediaEditDefaults = () => ({
                open: false,
                action: '',
                caption: '',
                name: '',
            });

            const mediaDeleteDefaults = () => ({
                open: false,
                action: '',
                name: '',
            });

            return {
                selectedFolderId,
                uploadLimits,
                labels,
                folderModal: folderDefaults(),
                folderDeleteModal: folderDeleteDefaults(),
                uploadModal: uploadDefaults(),
                mediaEditModal: mediaEditDefaults(),
                mediaDeleteModal: mediaDeleteDefaults(),
                openFolderModal(mode, payload) {
                    this.folderModal = {
                        ...folderDefaults(),
                        open: true,
                        mode,
                        title: payload.title,
                        submit: payload.submit,
                        action: payload.action,
                        name: payload.name ?? '',
                        description: payload.description ?? '',
                        parent_id: payload.parent_id !== null && payload.parent_id !== undefined ? String(payload.parent_id) : '',
                    };
                },
                openFolderDeleteModal(payload) {
                    this.folderDeleteModal = {
                        ...folderDeleteDefaults(),
                        open: true,
                        action: payload.action,
                        name: payload.name,
                    };
                },
                openUploadModal(payload) {
                    this.uploadModal = {
                        ...uploadDefaults(),
                        open: true,
                        action: payload.action,
                        name: payload.name,
                    };
                    this.$nextTick(() => {
                        if (this.$refs.uploadInput) {
                            this.$refs.uploadInput.value = '';
                        }
                    });
                },
                handleFileSelection(event) {
                    const files = Array.from(event.target.files ?? []).slice(0, this.uploadLimits.max_file_uploads);
                    this.uploadModal.files = files.map((file, index) => ({
                        id: `${Date.now()}-${index}`,
                        file,
                        name: file.name,
                        caption: '',
                        size: file.size,
                        type: file.type,
                    }));
                    this.syncUploadInput();
                },
                removeUploadFile(index) {
                    this.uploadModal.files.splice(index, 1);
                    if (this.uploadModal.files.length === 0 && this.$refs.uploadInput) {
                        this.$refs.uploadInput.value = '';
                    }
                    this.syncUploadInput();
                },
                syncUploadInput() {
                    if (! this.$refs.uploadInput || typeof DataTransfer === 'undefined') {
                        return;
                    }

                    const transfer = new DataTransfer();
                    this.uploadModal.files.forEach((item) => {
                        if (item.file) {
                            transfer.items.add(item.file);
                        }
                    });

                    this.$refs.uploadInput.files = transfer.files;
                },
                openMediaEditModal(payload) {
                    this.mediaEditModal = {
                        ...mediaEditDefaults(),
                        open: true,
                        action: payload.action,
                        caption: payload.caption ?? '',
                        name: payload.name ?? '',
                    };
                },
                openMediaDeleteModal(payload) {
                    this.mediaDeleteModal = {
                        ...mediaDeleteDefaults(),
                        open: true,
                        action: payload.action,
                        name: payload.name ?? '',
                    };
                },
                closeModal(key) {
                    switch (key) {
                        case 'folderModal':
                            this.folderModal = folderDefaults();
                            break;
                        case 'folderDeleteModal':
                            this.folderDeleteModal = folderDeleteDefaults();
                            break;
                        case 'uploadModal':
                            this.uploadModal = uploadDefaults();
                            if (this.$refs.uploadInput) {
                                this.$refs.uploadInput.value = '';
                            }
                            break;
                        case 'mediaEditModal':
                            this.mediaEditModal = mediaEditDefaults();
                            break;
                        case 'mediaDeleteModal':
                            this.mediaDeleteModal = mediaDeleteDefaults();
                            break;
                    }
                },
                copyMediaUrl(url) {
                    if (navigator?.clipboard?.writeText) {
                        navigator.clipboard.writeText(url);
                    } else {
                        const textarea = document.createElement('textarea');
                        textarea.value = url;
                        document.body.appendChild(textarea);
                        textarea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textarea);
                    }

                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: {
                            message: this.labels.notifyCopied,
                            type: 'success',
                        },
                    }));
                },
                formatFileSize(bytes) {
                    if (typeof bytes !== 'number' || Number.isNaN(bytes) || bytes <= 0) {
                        return '0 B';
                    }

                    const units = ['B', 'KB', 'MB', 'GB', 'TB'];
                    let size = bytes;
                    let unitIndex = 0;

                    while (size >= 1024 && unitIndex < units.length - 1) {
                        size /= 1024;
                        unitIndex += 1;
                    }

                    const decimals = unitIndex === 0 ? 0 : 2;
                    return `${size.toFixed(decimals)} ${units[unitIndex]}`;
                },
            };
        }
    </script>
</x-layouts.backend-layout>
