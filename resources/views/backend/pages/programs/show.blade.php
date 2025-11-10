<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            {{-- Program Header --}}
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $program->title }}</h2>
                    <p class="text-gray-600 dark:text-gray-300 mt-1">{{ $program->host }}</p>
                    @if($program->country)
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                            <iconify-icon icon="mdi:map-marker" class="w-4 h-4 text-blue-500"></iconify-icon>
                            {{ $program->country }}
                        </p>
                    @endif
                    <div class="mt-2">
                        <span class="{{ get_program_status_class($program->state->value) }} px-2.5 py-0.5 rounded-full text-xs font-medium">
                            {{ ucfirst(__($program->state->value)) }}
                        </span>
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    <x-buttons.button
                        as="a"
                        :href="route('admin.programs.edit', $program)"
                        variant="secondary"
                        icon="lucide:edit"
                        class="mr-2"
                    >
                        {{ __('Edit') }}
                    </x-buttons.button>
                    
                    <x-buttons.button
                        as="a"
                        :href="route('admin.programs.index')"
                        variant="primary"
                        icon="lucide:arrow-left"
                    >
                        {{ __('Back to Programs') }}
                    </x-buttons.button>
                </div>
            </div>

            {{-- Program Image --}}
            @if($program->hasImage())
                <div class="mb-6">
                    <img 
                        src="{{ $program->getImageUrl() }}" 
                        alt="{{ $program->title }}" 
                        class="rounded-lg shadow-md w-full max-w-2xl mx-auto"
                    >
                </div>
            @endif

            {{-- Primary Details --}}
            @php
                $contactPeople = collect($program->contact_people ?? [])
                    ->map(fn ($contact) => [
                        'name' => $contact['name'] ?? '',
                        'bio' => $contact['bio'] ?? '',
                        'contact' => $contact['contact'] ?? '',
                    ])
                    ->filter(fn ($contact) => $contact['name'] !== '' || $contact['bio'] !== '' || $contact['contact'] !== '')
                    ->values();
            @endphp

            {{-- Program Description --}}
                <div class="xl:col-span-2 space-y-6">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                            <iconify-icon icon="mdi:file-document-edit-outline" class="w-5 h-5 text-blue-500"></iconify-icon>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Program Overview') }}</h3>
                        </div>
                        <div class="p-6 prose dark:prose-invert max-w-none">
                            {!! $program->description !!}
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                            <iconify-icon icon="mdi:account-tie" class="w-5 h-5 text-blue-500"></iconify-icon>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Contact Persons') }}</h3>
                        </div>
                        <div class="p-6 space-y-4 text-sm text-gray-700 dark:text-gray-300">
                            @forelse($contactPeople as $index => $contact)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ __('Contact') }} {{ $index + 1 }}
                                        </h4>
                                    </div>
                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Name') }}</p>
                                            <p class="mt-1 text-base font-semibold text-gray-900 dark:text-white">
                                                {{ $contact['name'] ?: __('Not specified') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Contact Details') }}</p>
                                            <p class="mt-1 whitespace-pre-line">
                                                {{ $contact['contact'] ?: __('Not provided') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Bio') }}</p>
                                        <p class="mt-1 whitespace-pre-line">
                                            {{ $contact['bio'] ?: __('No bio information available.') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No contact persons provided for this program.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                            <iconify-icon icon="mdi:account-group" class="w-5 h-5 text-blue-500"></iconify-icon>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Partners Involved') }}</h3>
                        </div>
                        <div class="p-6 text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                            {{ $program->partners_involved ?: __('No partners listed for this program.') }}
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                            <iconify-icon icon="mdi:shape-outline" class="w-5 h-5 text-blue-500"></iconify-icon>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Program Categories') }}</h3>
                        </div>
                        <div class="p-6">
                            @forelse($program->category_labels as $label)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-200 mr-2 mb-2">
                                    {{ $label }}
                                </span>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No categories assigned.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center gap-2">
                            <iconify-icon icon="mdi:information-outline" class="w-5 h-5 text-blue-500"></iconify-icon>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Program Details') }}</h3>
                        </div>
                        <dl class="p-6 space-y-4 text-sm text-gray-700 dark:text-gray-300">
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Host Organisation') }}</dt>
                                <dd class="mt-1 text-base font-medium text-gray-900 dark:text-white">{{ $program->host }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Country') }}</dt>
                                <dd class="mt-1">{{ $program->country ?: __('Not specified') }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Current State') }}</dt>
                                <dd class="mt-1 flex items-center gap-2">
                                    <span class="{{ get_program_status_class($program->state->value) }} px-2.5 py-0.5 rounded-full text-xs font-medium">
                                        {{ ucfirst(__($program->state->value)) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Status Actions --}}
            @if(count($program->getAvailableActions()) > 0)
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ __('Status Actions') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($program->getAvailableActions() as $action => $config)
                            <button 
                                x-data="{}"
                                x-on:click="window.dispatchEvent(new CustomEvent('showChangeStatusModal', { 
                                    detail: { 
                                        programId: {{ $program->id }}, 
                                        action: '{{ $action }}', 
                                        config: {{ json_encode($config) }}
                                    } 
                                }))"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 {{ get_action_button_classes($config['color']) }}"
                                style="{{ get_action_button_style($config['color']) }} color: white !important;"
                                onmouseover="this.style.opacity='0.9'"
                                onmouseout="this.style.opacity='1'"
                            >
                                <iconify-icon icon="{{ $config['icon'] }}" class="w-4 h-4 mr-1.5" style="color: white !important;"></iconify-icon>
                                <span style="color: white !important;">{{ $config['label'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Program Metadata --}}
            <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-2">
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('Created At') }}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $program->created_at->format('M d, Y h:i A') }}
                        </dd>
                    </div>
                    <div class="sm:col-span-1">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                            {{ __('Last Updated') }}
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                            {{ $program->updated_at->format('M d, Y h:i A') }}
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    {{-- Status Change Modal --}}
    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('programStatus', () => ({
                showStatusModal: false,
                statusAction: null,
                statusConfig: null,
                statusProgramId: {{ $program->id }},
                statusComment: '',
                isLoading: false,
                
                init() {
                    // Initialize event listener for showing the modal
                    window.addEventListener('showChangeStatusModal', (event) => {
                        this.statusAction = event.detail.action;
                        this.statusConfig = event.detail.config;
                        this.statusProgramId = event.detail.programId || {{ $program->id }};
                        this.statusComment = '';
                        this.showStatusModal = true;
                    });
                },
                
                get isFormValid() {
                    if (!this.statusConfig) return false;
                    // Comment is optional, so form is always valid
                    return true;
                },
                
                async submitStatusChange() {
                    if (this.isLoading) return;
                    this.isLoading = true;
                    
                    try {
                        const response = await fetch(`/admin/programs/${this.statusProgramId}/change-status`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                action: this.statusAction,
                                comment: this.statusComment
                            })
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok) {
                            window.showToast('success', 'Success', data.message || 'Status updated successfully');
                            window.location.reload();
                        } else {
                            throw new Error(data.message || 'Failed to update status');
                        }
                    } catch (error) {
                        window.showToast('error', 'Error', error.message || 'An error occurred');
                    } finally {
                        this.isLoading = false;
                        this.showStatusModal = false;
                    }
                }
            }));
        });
    </script>
    @endpush

    <!-- Status Change Modal -->
    <div x-data="programStatus" x-cloak>
        <div x-show="showStatusModal" 
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center p-4 z-50"
             @click.self="showStatusModal = false"
             @keydown.escape.window="showStatusModal = false">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            <span x-show="statusConfig && statusConfig.label" x-text="statusConfig && statusConfig.label ? statusConfig.label : ''"></span>
                            <span x-show="!statusConfig || !statusConfig.label">{{ __('Change Status') }}</span>
                        </h3>
                        <button @click="showStatusModal = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                            <span x-show="statusConfig && statusConfig.message" x-text="statusConfig && statusConfig.message ? statusConfig.message : ''"></span>
                            <span x-show="!statusConfig || !statusConfig.message">{{ __('Are you sure you want to change the status of this program?') }}</span>
                        </p>
                
                        <div class="mt-4">
                            <label for="status_comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('Comment (Optional)') }}
                            </label>
                            <textarea 
                                id="status_comment" 
                                name="status_comment"
                                class="mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:text-white" 
                                rows="3" 
                                x-model="statusComment"
                                @input="statusComment = $event.target.value"
                                placeholder="{{ __('Enter a reason for this status change (optional)...') }}"
                            ></textarea>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button"
                                @click="showStatusModal = false"
                                :disabled="isLoading"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                {{ __('Cancel') }}
                            </button>
                            
                            <button 
                                type="button"
                                @click="submitStatusChange()"
                                :disabled="isLoading || !isFormValid"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                style="color: white !important; background-color: #4f46e5 !important;"
                            >
                                <span x-show="!isLoading" style="color: white !important; display: inline-flex;">
                                    <span x-text="statusConfig && statusConfig.label ? statusConfig.label : '{{ __('Confirm') }}'" style="color: white !important;"></span>
                                </span>
                                <span x-show="isLoading" class="flex items-center" style="color: white !important; display: inline-flex;">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4" style="color: white !important;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span style="color: white !important;">{{ __('Processing...') }}</span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.backend-layout>
