<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    {!! Hook::applyFilters('filter.page.create.after_breadcrumbs', '') !!}

    <form action="{{ route('admin.pages.store') }}" method="POST" enctype="multipart/form-data" data-prevent-unsaved-changes x-data="pageForm()">
        @csrf

        @include('backend.pages.pages.partials.form', [
            'page' => null,
            'mode' => 'create'
        ])
    </form>
    
    {!! Hook::applyFilters('filter.page.create.after_form', '') !!}

    @push('scripts')
        {{-- Quill editor for content --}}
        <x-quill-editor :editor-id="'content'" height="400px" maxHeight="-1" />

        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('pageForm', () => ({
                    is_custom_section: @json((bool) old('is_custom_section', false)),
                    show_in_nav: @json((bool) old('show_in_nav', true)),
                    show_in_footer: @json((bool) old('show_in_footer', false)),
                    
                    init() {
                        // Watch for custom section changes
                        this.$watch('is_custom_section', (value) => {
                            const sectionSelect = document.getElementById('section');
                            const customSectionField = document.getElementById('custom-section-field');
                            
                            if (value) {
                                sectionSelect.value = 'custom';
                                if (customSectionField) {
                                    customSectionField.classList.remove('hidden');
                                }
                            } else {
                                if (customSectionField) {
                                    customSectionField.classList.add('hidden');
                                }
                            }
                        });
                    },
                    
                    // Toggle custom section visibility based on select
                    toggleCustomSection() {
                        const sectionSelect = document.getElementById('section');
                        const customSectionField = document.getElementById('custom-section-field');
                        
                        if (sectionSelect.value === 'custom') {
                            this.is_custom_section = true;
                            if (customSectionField) {
                                customSectionField.classList.remove('hidden');
                            }
                        } else {
                            this.is_custom_section = false;
                            if (customSectionField) {
                                customSectionField.classList.add('hidden');
                            }
                        }
                    }
                }));
            });

            // Initialize form behavior
            document.addEventListener('DOMContentLoaded', function() {
                const sectionSelect = document.getElementById('section');
                if (sectionSelect) {
                    sectionSelect.addEventListener('change', function() {
                        const pageForm = Alpine.$data(document.querySelector('[x-data]'));
                        if (pageForm && pageForm.toggleCustomSection) {
                            pageForm.toggleCustomSection();
                        }
                    });
                }
            });
        </script>
    @endpush
</x-layouts.backend-layout>