@php
    /** @var Illuminate\Contracts\Pagination\LengthAwarePaginator $media */
@endphp

<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs">
            <x-slot name="title_after">
                <span class="badge">{{ ucfirst($category) }}</span>
                @if ($activeType)
                    <span class="badge">{{ __(ucwords(str_replace('_', ' ', $activeType))) }}</span>
                @endif
            </x-slot>
        </x-breadcrumbs>
    </x-slot>

    <div class="px-6">
        <div class="grid grid-cols-6 md:grid-cols-6 gap-2 mb-6">
            <div class="flex items-center p-4 rounded-md border bg-white dark:bg-gray-800">
                <iconify-icon icon="lucide:image" class="text-2xl text-green-500 mr-3"></iconify-icon>
                <div>
                    <div class="text-sm text-gray-500">{{ __('Images') }}</div>
                    <div class="text-2xl font-semibold">{{ $stats['images'] }}</div>
                </div>
            </div>
            <div class="flex items-center p-4 rounded-md border bg-white dark:bg-gray-800">
                <iconify-icon icon="lucide:video" class="text-2xl text-purple-500 mr-3"></iconify-icon>
                <div>
                    <div class="text-sm text-gray-500">{{ __('Videos') }}</div>
                    <div class="text-2xl font-semibold">{{ $stats['videos'] }}</div>
                </div>
            </div>
            <div class="flex items-center p-4 rounded-md border bg-white dark:bg-gray-800">
                <iconify-icon icon="lucide:music" class="text-2xl text-emerald-500 mr-3"></iconify-icon>
                <div>
                    <div class="text-sm text-gray-500">{{ __('Audios') }}</div>
                    <div class="text-2xl font-semibold">{{ $stats['audios'] }}</div>
                </div>
            </div>
            <div class="flex items-center p-4 rounded-md border bg-white dark:bg-gray-800">
                <iconify-icon icon="lucide:file-text" class="text-2xl text-orange-500 mr-3"></iconify-icon>
                <div class="">
                    <div class="text-sm text-gray-500">{{ __('Documents') }}</div>
                    <div class="text-2xl font-semibold">{{ $stats['documents'] }}</div>
                </div>
            </div>
            <div class="flex items-center p-4 rounded-md border bg-white dark:bg-gray-800">
                <iconify-icon icon="lucide:files" class="text-2xl text-blue-500 mr-3"></iconify-icon>
                <div>
                    <div class="text-sm text-gray-500">{{ __('Total') }}</div>
                    <div class="text-2xl font-semibold">{{ $stats['total'] }}</div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 border rounded-md p-4 mb-6">
            <form action="{{ route('admin.resources.store', ['category' => $category]) }}" method="post"
                enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm mb-1 font-bold">{{ __('Please Select Type') }}</label>
                        <select name="type" class="form-select w-fit bg-gray-50 p-2 rounded-2xl pr-6 cursor-pointer">
                            @foreach ($types as $key => $label)
                                <option value="{{ $key }}" @selected($key === $activeType)>{{ __($label) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center transition-colors cursor-pointer hover:border-primary hover:bg-primary-50 dark:hover:bg-primary-900/20"
                        id="drop-zone" onclick="document.getElementById('file-input').click()"
                        ondrop="dropHandler(event);" ondragover="dragOverHandler(event);"
                        ondragleave="dragLeaveHandler(event);">
                        <label class="block text-sm mb-1">{{ __('Files') }}</label>
                        <iconify-icon icon="lucide:upload-cloud"
                            class="text-4xl text-gray-400 mb-4 mx-auto"></iconify-icon>
                        <input type="file" id="file-input" name="files[]" multiple
                            class="form-input w-full hidden" />
                        <p class="text-xs text-gray-500 mt-1">
                            {{ __('You can upload multiple files. Versioning is automatic per original filename.') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit" class="btn btn-primary">{{ __('Upload') }}</button>
                    <a class="btn btn-secondary"
                        href="{{ route('admin.resources.index', ['category' => $category]) }}">{{ __('Refresh') }}</a>
                    <a class="btn" href="{{ route('admin.media.index') }}">{{ __('Open Media Library') }}</a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 border rounded-md">
            <div class="p-4 border-b flex items-center justify-between gap-3">
                <div class="font-semibold">{{ __('Files') }}</div>
                <form method="get" class="flex items-center gap-2">
                    @if ($activeType)
                        <input type="hidden" name="type" value="{{ $activeType }}" />
                    @endif
                    <input name="search" value="{{ request('search') }}"
                        placeholder="{{ __('Search name or file name') }}" class="form-input" />
                    <button class="btn">{{ __('Search') }}</button>
                </form>
            </div>

            <div class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse($media as $m)
                    <div class="border rounded-md p-3">
                        <div class="text-sm font-medium break-all">{{ $m->name }}</div>
                        <div class="text-xs text-gray-500 break-all">{{ $m->file_name }}</div>
                        <div class="text-xs text-gray-500">{{ __('Type') }}:
                            {{ __(ucwords(str_replace('_', ' ', $m->r_type))) }}</div>
                        <div class="text-xs text-gray-500">{{ __('Version') }}: v{{ $m->r_version }}</div>
                        <div class="text-xs mt-1">
                            @if (!$m->r_archived)
                                <span
                                    class="inline-block px-2 py-0.5 rounded bg-green-100 text-green-800 text-xs">{{ __('Visible') }}</span>
                            @else
                                <span
                                    class="inline-block px-2 py-0.5 rounded bg-yellow-100 text-yellow-800 text-xs">{{ __('Archived') }}</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500">{{ __('Size') }}: {{ $m->human_readable_size }}</div>
                        <div class="mt-2 flex items-center gap-2">
                            <a href="{{ $m->url }}" target="_blank"
                                class="btn btn-secondary btn-sm">{{ __('View') }}</a>
                            <button type="button" class="btn btn-danger btn-sm"
                                onclick="deleteResource({{ $m->id }})">{{ __('Delete') }}</button>
                            @can('make.archive')
                                @if (!$m->r_archived)
                                    <button type="button" class="btn btn-warning btn-sm"
                                        onclick="toggleArchive({{ $m->id }}, true)">{{ __('Archive') }}</button>
                                @else
                                    <button type="button" class="btn btn-success btn-sm"
                                        onclick="toggleArchive({{ $m->id }}, false)">{{ __('Unarchive') }}</button>
                                @endif
                            @endcan
                        </div>
                    </div>
                @empty
                    <div class="text-gray-500">{{ __('No files found.') }}</div>
                @endforelse
            </div>

            <div class="p-4">{{ $media->links() }}</div>
        </div>
    </div>

    <script>
        async function deleteResource(id) {
            if (!confirm('{{ __('Are you sure you want to delete this file?') }}')) return;
            const res = await fetch('{{ route('admin.resources.destroy', ['id' => 0]) }}'.replace('/0', '/' + id), {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            if (res.ok) {
                location.reload();
            } else {
                alert('Delete failed');
            }
        }

        async function toggleArchive(id, archive) {
            const url = archive ?
                '{{ route('admin.resources.archive', ['id' => 0]) }}'.replace('/0', '/' + id) :
                '{{ route('admin.resources.unarchive', ['id' => 0]) }}'.replace('/0', '/' + id);
            const res = await fetch(url, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            if (res.ok) {
                location.reload();
            } else if (res.status === 403) {
                alert('You do not have permission to perform this action.');
            } else {
                alert('Action failed');
            }
        }

        // Add drag and drop functionality
        function dragOverHandler(ev) {
            ev.preventDefault();
            ev.dataTransfer.dropEffect = "copy";
            document.getElementById('drop-zone').classList.add('border-primary', 'bg-primary-50', 'dark:bg-primary-900/20');
        }

        function dragLeaveHandler(ev) {
            ev.preventDefault();
            document.getElementById('drop-zone').classList.remove('border-primary', 'bg-primary-50',
                'dark:bg-primary-900/20');
        }

        function dropHandler(ev) {
            ev.preventDefault();
            document.getElementById('drop-zone').classList.remove('border-primary', 'bg-primary-50',
                'dark:bg-primary-900/20');

            const files = ev.dataTransfer.files;
            document.getElementById('file-input').files = files;
            document.getElementById('file-input').dispatchEvent(new Event('change'));
        }
    </script>
</x-layouts.backend-layout>
