<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <div x-data="{
        selectedDocuments: [],
        selectAll: false,
        bulkDeleteModalOpen: false,
        typeDropdownOpen: false,
        categoryDropdownOpen: false,
        statusDropdownOpen: false,
        bulkActionsDropdownOpen: false,
        uploadModalOpen: false,

        // Change status modal state
        changeStatusModalOpen: false,
        changeStatusDocumentId: null,
        changeStatusAction: null,
        changeStatusModalTitle: '',
        changeStatusModalMessage: '',
        changeStatusButtonText: '',
        changeStatusComment: '',
        changeStatusLoading: false,
    
        showSingleDeleteModal(id) {
            this.selectedDocuments = [id.toString()];
            this.bulkDeleteModalOpen = true;
        },

        showChangeStatusModal(documentId, action, actionConfig) {
            this.changeStatusDocumentId = documentId;
            this.changeStatusAction = action;
            this.changeStatusComment = '';
            this.changeStatusLoading = false;
            this.changeStatusModalTitle = actionConfig.label || 'Change Status';
            this.changeStatusModalMessage = this.getStatusChangeMessage(action);
            this.changeStatusButtonText = actionConfig.label || 'Confirm';
            this.changeStatusModalOpen = true;
        },

        getStatusChangeMessage(action) {
            const messages = {
                'send_for_review': '{{ __("This will send the resource for review.") }}',
                'approve': '{{ __("This will approve the resource for publication.") }}',
                'publish': '{{ __("This will publish the resource.") }}',
                'reject': '{{ __("This will send the resource back for changes.") }}',
                'send_back': '{{ __("This will send the resource back for review.") }}',
                'unpublish': '{{ __("This will unpublish the resource.") }}',
                'archive': '{{ __("This will archive the resource.") }}',
                'restore': '{{ __("This will restore the resource.") }}'
            };
            return messages[action] || '{{ __("Are you sure you want to change the resource status?") }}';
        },

        async performStatusChange() {
            if (this.changeStatusLoading) return;
            this.changeStatusLoading = true;
            try {
                const response = await fetch(`/admin/others/${this.changeStatusDocumentId}/change-status`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ action: this.changeStatusAction, comment: this.changeStatusComment })
                });
                const data = await response.json();
                if (data.success) {
                    if (window.showToast) window.showToast('success', '{{ __("Success") }}', data.message);
                    this.changeStatusModalOpen = false;
                    setTimeout(() => location.reload(), 800);
                } else {
                    throw new Error(data.message || 'Failed');
                }
            } catch (e) {
                if (window.showToast) window.showToast('error', '{{ __("Error") }}', e.message || 'Failed');
            } finally {
                this.changeStatusLoading = false;
            }
        }
    }" id="documentManager">
        @if ($errors->any())
            <div class="mb-6 p-4 border border-red-200 bg-red-50 rounded-md dark:border-red-800 dark:bg-red-900/20">
                <div class="flex">
                    <iconify-icon icon="lucide:alert-circle" class="text-red-500 text-xl mr-3 mt-0.5"></iconify-icon>
                    <div>
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-200 mb-2">
                            {{ __('Upload Error') }}
                        </h3>
                        <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-2 gap-4 mb-6 md:grid-cols-6 md:gap-6">
            <div class="p-4 bg-white rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center">
                    <iconify-icon icon="lucide:files" class="text-2xl text-blue-500 mr-3"></iconify-icon>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('Total Documents') }}</p>
                        <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center">
                    <iconify-icon icon="lucide:check-circle" class="text-2xl text-green-500 mr-3"></iconify-icon>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('Published') }}</p>
                        <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $stats['published'] }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center">
                    <iconify-icon icon="lucide:edit" class="text-2xl text-yellow-500 mr-3"></iconify-icon>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('Draft') }}</p>
                        <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $stats['draft'] }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center">
                    <iconify-icon icon="lucide:star" class="text-2xl text-purple-500 mr-3"></iconify-icon>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('Featured') }}</p>
                        <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $stats['featured'] }}</p>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center">
                    <iconify-icon icon="lucide:download" class="text-2xl text-orange-500 mr-3"></iconify-icon>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('Total Downloads') }}</p>
                        <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $stats['total_downloads'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-white rounded-md border border-gray-200 dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="flex items-center">
                    <iconify-icon icon="lucide:eye" class="text-2xl text-red-500 mr-3"></iconify-icon>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-300">{{ __('Under Review') }}</p>
                        <p class="text-lg font-semibold text-gray-700 dark:text-white">{{ $stats['under_review'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-md border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
                <div class="px-5 py-4 sm:px-6 sm:py-5 flex flex-col md:flex-row justify-between items-center gap-3">
                    @include('backend.partials.search-form', [
                        'placeholder' => __('Search documents...'),
                    ])

                    <div class="flex items-center gap-3">
                        <!-- Bulk Actions dropdown -->
                        <div class="flex items-center justify-center relative" x-show="selectedDocuments.length > 0">
                            <button @click="bulkActionsDropdownOpen = !bulkActionsDropdownOpen"
                                class="btn-secondary flex items-center justify-center gap-2 text-sm" type="button">
                                <iconify-icon icon="lucide:more-vertical"></iconify-icon>
                                <span>{{ __('Bulk Actions') }} (<span x-text="selectedDocuments.length"></span>)</span>
                                <iconify-icon icon="lucide:chevron-down"></iconify-icon>
                            </button>

                            <!-- Bulk Actions dropdown menu -->
                            <div x-show="bulkActionsDropdownOpen" @click.away="bulkActionsDropdownOpen = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute top-full right-0 z-10 w-48 p-2 bg-white rounded-md shadow-lg dark:bg-gray-700 mt-2">
                                <ul class="space-y-2">
                                    <li class="cursor-pointer flex items-center gap-2 text-sm text-red-600 dark:text-red-500 hover:bg-red-50 dark:hover:bg-red-500 dark:hover:text-red-50 px-2 py-1.5 rounded transition-colors duration-300"
                                        @click="bulkDeleteModalOpen = true; bulkActionsDropdownOpen = false">
                                        <iconify-icon icon="lucide:trash"></iconify-icon>
                                        {{ __('Delete Selected') }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Type Filter dropdown -->
                        <div class="flex items-center justify-center relative">
                            <button @click="typeDropdownOpen = !typeDropdownOpen"
                                class="btn-secondary flex items-center justify-center gap-2 text-sm" type="button">
                                <iconify-icon icon="lucide:filter"></iconify-icon>
                                <span class="hidden sm:inline">{{ __('Type') }}</span>
                                @if (request('type'))
                                    <span
                                        class="px-2 py-0.5 text-xs bg-blue-100 text-blue-800 rounded-full dark:bg-blue-900/20 dark:text-primary">
                                        {{ ucfirst(request('type')) }}
                                    </span>
                                @endif
                                <iconify-icon icon="lucide:chevron-down"></iconify-icon>
                            </button>

                            <!-- Type dropdown menu -->
                            <div x-show="typeDropdownOpen" @click.away="typeDropdownOpen = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute top-full right-0 z-10 w-48 p-2 bg-white rounded-md shadow-lg dark:bg-gray-700 mt-2">
                                <ul class="space-y-2">
                                    <li>
                                        <a href="{{ route('admin.others.index', array_merge(request()->query(), ['type' => null])) }}"
                                            @click="typeDropdownOpen = false"
                                            class="cursor-pointer flex items-center gap-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-400 hover:opacity-80 px-2 py-1.5 rounded transition-colors duration-300 {{ !request('type') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                            <iconify-icon icon="lucide:layers"
                                                class="text-gray-500 dark:text-gray-400"></iconify-icon>
                                            {{ __('All Types') }}
                                        </a>
                                    </li>
                                    @foreach ($documentTypes as $type)
                                        <li>
                                            <a href="{{ route('admin.others.index', array_merge(request()->query(), ['type' => $type])) }}"
                                                @click="typeDropdownOpen = false"
                                                class="cursor-pointer flex items-center gap-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 px-2 py-1.5 rounded transition-colors duration-300 {{ request('type') === $type ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                                <iconify-icon icon="lucide:file-text"
                                                    class="text-blue-500"></iconify-icon>
                                                {{ $type }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- Category Filter dropdown -->
                        <div class="flex items-center justify-center relative">
                            <button @click="categoryDropdownOpen = !categoryDropdownOpen"
                                class="btn-secondary flex items-center justify-center gap-2 text-sm" type="button">
                                <iconify-icon icon="lucide:folder"></iconify-icon>
                                <span class="hidden sm:inline">{{ __('Category') }}</span>
                                @if (request('category'))
                                    <span
                                        class="px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full dark:bg-green-900/20 dark:text-green-300">
                                        {{ ucfirst(request('category')) }}
                                    </span>
                                @endif
                                <iconify-icon icon="lucide:chevron-down"></iconify-icon>
                            </button>

                            <!-- Category dropdown menu -->
                            <div x-show="categoryDropdownOpen" @click.away="categoryDropdownOpen = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute top-full right-0 z-10 w-48 p-2 bg-white rounded-md shadow-lg dark:bg-gray-700 mt-2">
                                <ul class="space-y-2">
                                    <li>
                                        <a href="{{ route('admin.others.index', array_merge(request()->query(), ['category' => null])) }}"
                                            @click="categoryDropdownOpen = false"
                                            class="cursor-pointer flex items-center gap-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-400 hover:opacity-80 px-2 py-1.5 rounded transition-colors duration-300 {{ !request('category') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                            <iconify-icon icon="lucide:layers"
                                                class="text-gray-500 dark:text-gray-400"></iconify-icon>
                                            {{ __('All Categories') }}
                                        </a>
                                    </li>
                                    @foreach ($categories as $category)
                                        <li>
                                            <a href="{{ route('admin.others.index', array_merge(request()->query(), ['category' => $category->name])) }}"
                                                @click="categoryDropdownOpen = false"
                                                class="cursor-pointer flex items-center gap-2 text-sm text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-600 px-2 py-1.5 rounded transition-colors duration-300 {{ request('category') === $category->name ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                                <iconify-icon icon="lucide:folder"
                                                    class="text-green-500"></iconify-icon>
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        @if (auth()->user()->can('others.create'))
                            <button @click="uploadModalOpen = true" class="btn-primary flex items-center gap-2">
                                <iconify-icon icon="lucide:upload" height="16"></iconify-icon>
                                {{ __('Upload File') }}
                            </button>
                        @endif
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-800">
                    <!-- List View Only -->
                    <div class="overflow-x-auto">
                        @if ($documents->count() > 0)
                            <table class="table">
                                <thead class="table-thead">
                                    <tr class="table-tr">
                                        <th width="3%" class="table-thead-th">
                                            <input type="checkbox" x-model="selectAll"
                                                @click="selectAll = !selectAll; selectedDocuments = selectAll ? [...document.querySelectorAll('.document-checkbox')].map(cb => cb.value) : [];"
                                                class="form-checkbox">
                                        </th>
                                        <th class="table-thead-th">
                                            {{ __('Document') }}
                                        </th>
                                        <th class="table-thead-th">
                                            {{ __('Author') }}
                                        </th>
                                        <th class="table-thead-th">
                                            {{ __('Type') }}
                                        </th>
                                        <th class="table-thead-th">
                                            {{ __('Category') }}
                                        </th>
                                        <th class="table-thead-th">
                                            {{ __('Status') }}
                                        </th>
                                        <th class="table-thead-th">
                                            {{ __('S. Actions') }}
                                        </th>
                                        <th class="table-thead-th">
                                            {{ __('Downloads') }}
                                        </th>
                                        <th class="table-thead-th">
                                            {{ __('Date') }}
                                        </th>
                                        <th class="table-thead-th table-thead-th-last">
                                            {{ __('Actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="table-tbody">
                                    @foreach ($documents as $document)
                                        <tr class="table-tr">
                                            <td class="table-td table-td-checkbox">
                                                <input type="checkbox" value="{{ $document->id }}"
                                                    x-model="selectedDocuments"
                                                    class="document-checkbox form-checkbox"
                                                    :class="selectedDocuments.includes('{{ $document->id }}') ? 'opacity-100' :
                                                        'opacity-0 group-hover:opacity-100'">
                                            </td>
                                            <td class="table-td">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10 mr-3">
                                                        <div
                                                            class="h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded flex items-center justify-center border border-blue-200 dark:border-blue-800">
                                                            <iconify-icon icon="lucide:file-text"
                                                                class="text-blue-600 dark:text-blue-400"></iconify-icon>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $document->title }}
                                                        </p>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            {{ $document->file_name }} •
                                                            {{ $document->file_size_formatted }}
                                                        </p>
                                                        @if ($document->is_featured)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300 mt-1">
                                                                <iconify-icon icon="lucide:star"
                                                                    class="w-3 h-3 mr-1"></iconify-icon>
                                                                {{ __('Featured') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="table-td">
                                                <p class="text-sm text-gray-900 dark:text-white">
                                                    {{ $document->creator }}</p>
                                            </td>
                                            <td class="table-td">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                                                    {{ $document->resource_type }}
                                                </span>
                                            </td>
                                            <td class="table-td">
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                                                    {{ $document->subject_area }}
                                                </span>
                                            </td>
                                            <td class="table-td">
                                                @php
                                                    $statusColors = [
                                                        'draft' =>
                                                            'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
                                                        'under_review' =>
                                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300',
                                                        'approved' =>
                                                            'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300',
                                                        'published' =>
                                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300',
                                                        'archived' =>
                                                            'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300',
                                                    ];
                                                @endphp
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusColors[$document->status] }}">
                                                    {{ ucfirst(str_replace('_', ' ', $document->status)) }}
                                                </span>
                                            </td>
                                            <td class="table-td">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach($document->getAvailableActions() as $action => $config)
                                                        <button type="button" x-on:click="showChangeStatusModal({{ $document->id }}, '{{ $action }}', {{ json_encode($config) }})"
                                                            class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md 
                                                                   bg-{{ $config['color'] }}-100 text-{{ $config['color'] }}-800 
                                                                   hover:bg-{{ $config['color'] }}-200 dark:bg-{{ $config['color'] }}-900/20 
                                                                   dark:text-{{ $config['color'] }}-300 dark:hover:bg-{{ $config['color'] }}-900/30
                                                                   transition-colors duration-200"
                                                            title="{{ $config['label'] }}">
                                                            <iconify-icon icon="{{ $config['icon'] }}" class="w-3 h-3 mr-1"></iconify-icon>
                                                            {{ $config['label'] }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="table-td">
                                                <div class="flex items-center text-sm text-gray-900 dark:text-white">
                                                    <iconify-icon icon="lucide:download"
                                                        class="w-4 h-4 mr-1 text-gray-400"></iconify-icon>
                                                    {{ $document->download_count }}
                                                </div>
                                            </td>
                                            <td class="table-td">
                                                <div class="text-sm text-gray-900 dark:text-white">
                                                    {{ ($document->published_at ?? $document->created_at)?->format('M d, Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $document->created_at->diffForHumans() }}
                                                </div>
                                            </td>
                                            <td class="table-td text-center">
                                                <div class="flex items-center justify-center gap-2">
                                                    <a href="{{ $document->download_url }}"
                                                        class="text-blue-400 hover:text-blue-600 dark:hover:text-blue-300"
                                                        title="{{ __('Download') }}"
                                                        onclick="incrementDownloadCount({{ $document->id }})">
                                                        <iconify-icon icon="lucide:download"
                                                            class="text-sm"></iconify-icon>
                                                    </a>
                                                    <button
                                                        class="text-green-400 hover:text-green-600 dark:hover:text-green-300"
                                                        onclick="openPdfModal('{{ Storage::disk('public')->url($document->file_path) }}', '{{ $document->title }}')"
                                                        title="{{ __('Preview') }}">
                                                        <iconify-icon icon="lucide:eye"
                                                            class="text-sm"></iconify-icon>
                                                    </button>
                                                    @if (auth()->user()->can('document.edit'))
                                                        <a href="{{ route('admin.others.edit', $document->id) }}"
                                                            class="text-yellow-400 hover:text-yellow-600 dark:hover:text-yellow-300"
                                                            title="{{ __('Edit') }}">
                                                            <iconify-icon icon="lucide:edit"
                                                                class="text-sm"></iconify-icon>
                                                        </a>
                                                    @endif
                                                    @if (auth()->user()->can('document.delete'))
                                                        <button class="text-red-400 hover:text-red-600"
                                                            x-on:click="showSingleDeleteModal({{ $document->id }})"
                                                            title="{{ __('Delete') }}">
                                                            <iconify-icon icon="lucide:trash"
                                                                class="text-sm"></iconify-icon>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="text-center py-12">
                                <iconify-icon icon="lucide:file-text"
                                    class="text-6xl text-gray-300 dark:text-gray-600 mb-4 mx-auto"></iconify-icon>
                                <p class="text-gray-500 dark:text-gray-400 mb-4">{{ __('No documents found') }}</p>

                                @if (auth()->user()->can('others.create'))
                                    <div class="flex justify-center">
                                        <button @click="uploadModalOpen = true"
                                            class="btn-primary flex items-center gap-2">
                                            <iconify-icon icon="lucide:upload" height="16"></iconify-icon>
                                            {{ __('Upload File') }}
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Pagination -->
                    <div class="px-5 py-4">
                        {{ $documents->links() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Modal -->
        @include('backend.pages.Others.partials.upload-modal')

        <!-- Bulk Delete Modal -->
        @include('backend.pages.others.partials.bulk-delete-modal')

        <!-- PDF Modal (keep the same PDF modal from your original code) -->
        <div id="pdfModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-75" onclick="closePdfModal()">
            <!-- Your existing PDF modal code here -->
        </div>

        @include('backend.pages.document.partials.workflow-tracker')
    </div>

    @push('scripts')
        <script>
            function incrementDownloadCount(documentId) {
                // You can make an AJAX call here to increment the download count
                fetch(`/admin/others/${documentId}/increment-download`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                }).catch(error => console.error('Error:', error));
            }

            // Delete a single others resource
            function performSingleDelete(id) {
                if (!confirm('{{ __('Are you sure you want to delete this resource? This action cannot be undone.') }}')) {
                    return;
                }
                const url = "{{ route('admin.others.destroy', ':id') }}".replace(':id', id);
                fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(async (response) => {
                    if (!response.ok) {
                        const text = await response.text();
                        throw new Error(text || `HTTP ${response.status}`);
                    }
                    return response.json().catch(() => ({ success: true }));
                })
                .then(() => {
                    location.reload();
                })
                .catch(err => {
                    alert(err.message || 'Failed to delete resource');
                });
            }

            // Keep your existing PDF modal functions
            function openPdfModal(url, title) {
                const modal = document.getElementById('pdfModal');
                const iframe = document.getElementById('modalPdfViewer');
                const titleElement = document.getElementById('modalPdfTitle');
                const downloadLink = document.getElementById('pdfDownloadLink');

                iframe.src = url;
                titleElement.textContent = title || 'PDF Document';
                downloadLink.href = url;
                downloadLink.download = title || 'document.pdf';

                modal.classList.remove('hidden');
                modal.classList.add('flex', 'items-center', 'justify-center');
            }

            function closePdfModal() {
                const modal = document.getElementById('pdfModal');
                const iframe = document.getElementById('modalPdfViewer');
                iframe.src = '';
                modal.classList.add('hidden');
                modal.classList.remove('flex', 'items-center', 'justify-center');
            }
        </script>
    @endpush
</x-layouts.backend-layout>
