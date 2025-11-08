<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs">
            <x-slot name="title_after">
                @if (request('status'))
                    <span class="badge">{{ ucfirst(request('status')) }}</span>
                @endif
            </x-slot>
        </x-breadcrumbs>
    </x-slot>
  
    {!! Hook::applyFilters(\App\Enums\Hooks\ProgramFilterHook::PROGRAMS_AFTER_BREADCRUMBS->value, '', Program::class) !!}

    <div x-data="{
        // Properties for change status modal
        changeStatusModalOpen: false,
        changeStatusProgramId: null,
        changeStatusAction: null,
        changeStatusModalTitle: '',
        changeStatusModalMessage: '',
        changeStatusButtonText: '',
        changeStatusComment: '',
        changeStatusLoading: false,

        // Properties for bulk delete modal
        bulkDeleteModalOpen: false,
        selectedItems: [], // This will be managed by Livewire, but Alpine needs it for the modal
        bulkDeleteLoading: false,

        showChangeStatusModal(programId, action, actionConfig) {
            this.changeStatusProgramId = programId;
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
                'activate': '{{ __("This will activate the program.") }}',
                'pause': '{{ __("This will pause the program.") }}',
                'archive': '{{ __("This will archive the program.") }}',
                'restore': '{{ __("This will restore the program to draft status.") }}'
            };
            return messages[action] || '{{ __("Are you sure you want to change the program status?") }}';
        },

        async performStatusChange() {
            if (this.changeStatusLoading) return;
            this.changeStatusLoading = true;

            try {
                // Dispatch Livewire event to handle status change
                Livewire.dispatch('changeProgramStatus', {
                    programId: this.changeStatusProgramId,
                    action: this.changeStatusAction,
                    comment: this.changeStatusComment
                });

                this.changeStatusModalOpen = false;
                // Livewire will handle refresh and toast
            } catch (error) {
                console.error('Status change failed:', error);
                if (window.showToast) {
                    window.showToast('error', '{{ __('Error') }}', error.message);
                }
            } finally {
                this.changeStatusLoading = false;
            }
        },

        showDeleteModal(programId) {
            this.selectedItems = [programId]; // Set selected item for single delete
            this.bulkDeleteModalOpen = true;
        },

        async performBulkDelete() {
            if (this.bulkDeleteLoading) return;
            this.bulkDeleteLoading = true;

            try {
                // Dispatch Livewire event for bulk delete
                Livewire.dispatch('bulkDelete', { ids: this.selectedItems });
                this.bulkDeleteModalOpen = false;
            } catch (error) {
                console.error('Bulk delete failed:', error);
                if (window.showToast) {
                    window.showToast('error', '{{ __('Error') }}', error.message || '{{ __("Failed to delete programs") }}');
                }
            } finally {
                this.bulkDeleteLoading = false;
            }
        }
    }" id="programManager">
        @livewire('datatable.program-datatable', ['lazy' => true])

        @include('backend.pages.programs.partials.workflow-tracker')
        @include('backend.pages.document.partials.bulk-delete-modal') {{-- Reusing document's bulk delete modal --}}
    </div>

    {!! Hook::applyFilters(\App\Enums\Hooks\ProgramFilterHook::PROGRAMS_AFTER_TABLE->value, '', Program::class) !!}
</x-layouts.backend-layout>
