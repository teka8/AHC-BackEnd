<div x-cloak x-show="uploadModalOpen" x-transition.opacity.duration.200ms x-trap.inert.noscroll="uploadModalOpen"
    x-on:keydown.esc.window="uploadModalOpen = false" x-on:click.self="uploadModalOpen = false"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 p-4 backdrop-blur-md" role="dialog"
    aria-modal="true">

    <div x-show="uploadModalOpen" x-transition:enter="transition ease-out duration-200 delay-100"
        x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
        class="flex max-w-4xl w-full flex-col gap-4 overflow-hidden rounded-md border border-gray-100 dark:border-gray-800 bg-white text-gray-900 dark:bg-gray-700 dark:text-gray-300 max-h-[90vh] overflow-y-auto">

        <div
            class="flex items-center justify-between border-b border-gray-100 px-6 py-4 dark:border-gray-800 sticky top-0 bg-white dark:bg-gray-700 z-10">
            <h3 class="font-semibold tracking-wide text-gray-700 dark:text-white">
                {{ __('Upload Other Documents to Repository') }}
            </h3>
            <button x-on:click="uploadModalOpen = false" aria-label="close modal"
                class="text-gray-400 hover:bg-gray-200 hover:text-gray-700 rounded-md p-1 dark:hover:bg-gray-600 dark:hover:text-white">
                <iconify-icon icon="lucide:x" class="w-5 h-5"></iconify-icon>
            </button>
        </div>

        <div class="px-6 pb-6">
            <form id="upload-form" enctype="multipart/form-data">
                @csrf

                <!-- Document Metadata Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-700 dark:text-white mb-4">{{ __('Document Information') }}
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Title -->
                        <div class="col-span-2">
                            <label for="document_title"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Document Title') }} *
                            </label>
                            <input type="text" id="document_title" name="title" required
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                        </div>

                        <!-- Author -->
                        <div>
                            <label for="document_author"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Author') }} *
                            </label>
                            <input type="text" id="document_author" name="author" required
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                        </div>

                        <!-- Publication Date -->
                        <div>
                            <label for="publication_date"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Publication Date') }} *
                            </label>
                            <input type="date" id="publication_date" name="publication_date" required
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                        </div>
                    </div>

                    <!-- Abstract -->
                    <div class="mb-4">
                        <label for="document_abstract"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            {{ __('Abstract / Summary') }} *
                        </label>
                        <textarea id="document_abstract" name="abstract" required rows="4"
                            class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white"
                            placeholder="{{ __('Provide a brief summary of the document content...') }}"></textarea>
                    </div>

                    <!-- Document Type and Category -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Document Type -->
                        <div>
                            <label for="document_type"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Document Type') }} *
                            </label>
                            <select id="document_type" name="document_type" required
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                <option value="">{{ __('Select Document Type') }}</option>
                                <option value="Newsletter">{{ __('Newsletter') }}</option>
                                <option value="Presentation">{{ __('Presentation') }}</option>
                            </select>
                        </div>

                        <!-- Category -->
                        <div>
                            <label for="document_category"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Category') }} *
                            </label>
                            <select id="document_category" name="category" required
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                <option value="">{{ __('Select Category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Version -->
                        <div>
                            <label for="document_version"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Version') }}
                            </label>
                            <input type="text" id="document_version" name="version" value="1.0" placeholder="1.0"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                        </div>

                        <!-- Featured Document -->
                        <div class="flex items-center">
                            <input type="checkbox" id="is_featured" name="is_featured" value="1"
                                class="rounded border-gray-300 text-primary focus:ring-primary dark:border-gray-600 dark:bg-gray-800">
                            <label for="is_featured" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                {{ __('Feature this document') }}
                            </label>
                        </div>

                        <!-- Access Level -->
                        <div>
                            <label for="access_level"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('Access Level') }}
                            </label>
                            <select id="access_level" name="access_level"
                                class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white">
                                <option value="public">{{ __('Public') }}</option>
                                <option value="partner_only">{{ __('Partner Universities Only') }}</option>
                                <option value="internal_only">{{ __('Internal Only') }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- File Upload Section -->
                <div class="mb-6">
                    <h4 class="text-lg font-medium text-gray-700 dark:text-white mb-4">{{ __('File Upload') }}</h4>

                    <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center transition-colors cursor-pointer hover:border-primary hover:bg-primary-50 dark:hover:bg-primary-900/20"
                        id="drop-zone" onclick="document.getElementById('file-input').click()"
                        ondrop="dropHandler(event);" ondragover="dragOverHandler(event);"
                        ondragleave="dragLeaveHandler(event);">
                        <iconify-icon icon="lucide:upload-cloud"
                            class="text-4xl text-gray-400 mb-4 mx-auto"></iconify-icon>
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            {{ __('Drag and drop your document here, or click to select files') }}
                        </p>
                        <input type="file" id="file-input" name="files[]" class="hidden">
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-2 space-y-1">
                            <p>{{ __('Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, CSV') }}</p>
                            <p>{{ __('Maximum file size:') }} <span
                                    class="font-medium">{{ $uploadLimits['effective_max_filesize_formatted'] }}</span>
                            </p>
                            <p>{{ __('Maximum files at once:') }} <span
                                    class="font-medium">{{ $uploadLimits['max_file_uploads'] }}</span></p>
                            <p>{{ __('Maximum total upload:') }} <span
                                    class="font-medium">{{ $uploadLimits['post_max_size_formatted'] }}</span></p>

                            @if (config('app.demo_mode', false))
                                <p class="text-orange-600 dark:text-orange-400 font-medium">
                                    <iconify-icon icon="lucide:info" class="inline w-3 h-3 mr-1"></iconify-icon>
                                    {{ __('Demo Mode: Only document files are allowed.') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <div id="file-preview" class="mt-4 hidden">
                        <h4 class="font-medium text-gray-700 dark:text-white mb-2">{{ __('Selected Document:') }}</h4>
                        <div id="file-list" class="space-y-2"></div>
                    </div>
                </div>

                <!-- Tags Section -->
                <div class="mb-6">
                    <label for="document_tags"
                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        {{ __('Tags') }}
                    </label>
                    <input type="text" id="document_tags" name="tags"
                        class="w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white"
                        placeholder="{{ __('Add relevant tags separated by commas (e.g., research, health, education)') }}">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        {{ __('Add tags to make this document easier to find through search') }}
                    </p>
                </div>
            </form>

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-gray-600">
                <button type="button" x-on:click="uploadModalOpen = false" class="btn-default">
                    {{ __('Cancel') }}
                </button>
                <button type="button" id="upload-btn" onclick="uploadDocument()" class="btn-primary">
                    {{ __('Upload to Repository') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const uploadLimits = @json($uploadLimits);
    const isDemoMode = {{ config('app.demo_mode', false) ? 'true' : 'false' }};
    const allowedDemoMimeTypes = @json(config('app.demo_mode', false) ? \App\Support\Helper\MediaHelper::getAllowedMimeTypesForDemo() : []);


    // Set today's date as default publication date
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('publication_date').value = today;
    });

    // Function to check if file type is allowed in demo mode
    function isFileAllowedInDemo(fileType) {
        if (!isDemoMode) return true;
        return allowedDemoMimeTypes.includes(fileType);
    }

    document.getElementById('file-input').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        const preview = document.getElementById('file-preview');
        const fileList = document.getElementById('file-list');

        // Validate files before showing them
        const validFiles = [];
        const errors = [];

        if (files.length > uploadLimits.max_file_uploads) {
            errors.push(
                `{{ __('You can upload a maximum of :max files at once.', ['max' => '']) }}${uploadLimits.max_file_uploads}`
            );
        }

        let totalSize = 0;
        files.forEach((file, index) => {
            totalSize += file.size;

            // Check demo mode restrictions
            if (isDemoMode && !isFileAllowedInDemo(file.type)) {
                errors.push(
                    `{{ __('File ":name" is not allowed in demo mode. Only document files are permitted.', ['name' => '']) }}${file.name}"`
                );
                return;
            }

            if (file.size > uploadLimits.effective_max_filesize) {
                errors.push(
                    `{{ __('File ":name" exceeds the maximum size of :max', ['name' => '', 'max' => '']) }}${file.name}" exceeds ${uploadLimits.effective_max_filesize_formatted}`
                );
            } else {
                validFiles.push(file);
            }
        });

        if (totalSize > uploadLimits.post_max_size) {
            errors.push(
                `{{ __('Total upload size exceeds the limit of :max', ['max' => '']) }}${uploadLimits.post_max_size_formatted}`
            );
        }

        if (errors.length > 0) {
            alert(errors.join('\n'));
            this.value = '';
            preview.classList.add('hidden');
            return;
        }

        if (validFiles.length > 0) {
            preview.classList.remove('hidden');
            fileList.innerHTML = '';

            validFiles.forEach((file, index) => {
                const fileItem = document.createElement('div');
                fileItem.className =
                    'flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded';
                fileItem.innerHTML = `
                <div class="flex items-center">
                    <iconify-icon icon="lucide:file-text" class="text-gray-400 mr-2"></iconify-icon>
                    <span class="text-sm text-gray-700 dark:text-gray-300">${file.name}</span>
                    <span class="text-xs text-gray-500 ml-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
                </div>
                <button type="button" onclick="removeFile(${index})" class="text-red-500 hover:text-red-700">
                    <iconify-icon icon="lucide:x" class="w-4 h-4"></iconify-icon>
                </button>
            `;
                fileList.appendChild(fileItem);
            });
        } else {
            preview.classList.add('hidden');
        }
    });

    function removeFile(index) {
        const fileInput = document.getElementById('file-input');
        const dt = new DataTransfer();
        const files = Array.from(fileInput.files);

        files.splice(index, 1);

        for (const file of files) {
            dt.items.add(file);
        }

        fileInput.files = dt.files;
        fileInput.dispatchEvent(new Event('change'));
    }

    function uploadDocument() {
        const fileInput = document.getElementById('file-input');
        const uploadBtn = document.getElementById('upload-btn');
        const form = document.getElementById('upload-form');

        // Validate required fields
        const requiredFields = ['title', 'author', 'publication_date', 'abstract', 'document_type', 'category'];
        const missingFields = [];

        requiredFields.forEach(field => {
            const element = form.querySelector(`[name="${field}"]`);
            if (!element.value.trim()) {
                missingFields.push(element.previousElementSibling.textContent.trim());
            }
        });

        if (missingFields.length > 0) {
            alert(`{{ __('Please fill in all required fields:') }}\n${missingFields.join('\n')}`);
            return;
        }

        if (fileInput.files.length === 0) {
            alert('{{ __('Please select a document file to upload') }}');
            return;
        }

        const formData = new FormData(form);

        uploadBtn.disabled = true;
        uploadBtn.textContent = '{{ __('Uploading Document...') }}';

        // Use the Others repository route
        fetch('{{ route('admin.others.store') }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            })
            .then(async (response) => {
                const contentType = response.headers.get('content-type') || '';
                // Try to parse JSON only when the server returns JSON
                if (!response.ok) {
                    if (contentType.includes('application/json')) {
                        const errorData = await response.json();
                        const msg = errorData.message || `HTTP ${response.status}: ${response.statusText}`;
                        throw new Error(msg);
                    } else {
                        const text = await response.text();
                        throw new Error(
                            `Unexpected response (status ${response.status}). If you were redirected to login or got a 419/CSRF page, please refresh and try again.\n\n${text.slice(0, 500)}...`
                        );
                    }
                }
                if (contentType.includes('application/json')) {
                    return response.json();
                } else {
                    const text = await response.text();
                    throw new Error(`Server returned non-JSON response.\n\n${text.slice(0, 500)}...`);
                }
            })
            .then(data => {
                if (data.success) {
                    if (window.showToast) {
                        window.showToast('success', '{{ __('Success') }}', data.message ||
                            '{{ __('Document uploaded successfully') }}');
                    }
                    // Close modal and refresh the page or update the document list
                    document.querySelector('[x-on\\:click="uploadModalOpen = false"]').click();
                    if (typeof reloadDocumentList === 'function') {
                        reloadDocumentList();
                    } else {
                        location.reload();
                    }
                } else {
                    let errorMessage = data.message || '{{ __('Error uploading document') }}';

                    // Handle validation errors
                    if (data.errors) {
                        const validationErrors = Object.values(data.errors).flat();
                        errorMessage = validationErrors.join('\n');
                    }

                    if (data.error_type === 'php_upload_limit') {
                        errorMessage +=
                            `\n\n{{ __('Upload size:') }} ${Math.round(data.uploaded_size / 1024 / 1024)} MB\n{{ __('PHP Limit:') }} ${data.limit_formatted}`;
                    }

                    alert(errorMessage);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || '{{ __('Error uploading document') }}');
            })
            .finally(() => {
                uploadBtn.disabled = false;
                uploadBtn.textContent = '{{ __('Upload to Repository') }}';
            });
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
