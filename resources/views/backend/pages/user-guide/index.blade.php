<x-layouts.backend-layout :breadcrumbs="$breadcrumbs">
    <x-slot name="breadcrumbsData">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    </x-slot>

    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('AHC User Guide') }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ __('Search the guide and download it as a file.') }}</p>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="w-full sm:w-80">
                        <input
                            id="user-guide-search"
                            type="text"
                            placeholder="{{ __('Search...') }}"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-primary focus:border-primary"
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <button id="user-guide-prev" type="button" class="btn btn-secondary px-3" disabled>
                            <iconify-icon icon="lucide:chevron-up"></iconify-icon>
                        </button>
                        <button id="user-guide-next" type="button" class="btn btn-secondary px-3" disabled>
                            <iconify-icon icon="lucide:chevron-down"></iconify-icon>
                        </button>
                        <span id="user-guide-counter" class="text-sm text-gray-600 dark:text-gray-300 min-w-14 text-center hidden"></span>
                    </div>

                    <button id="download-pdf-btn" class="btn btn-primary whitespace-nowrap" onclick="downloadUserGuide()">
                        <iconify-icon icon="lucide:download" class="mr-2"></iconify-icon>
                        <span id="download-text">{{ __('Download User Guide') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div id="user-guide-floating-nav" class="hidden fixed bottom-6 right-6 z-50">
            <div class="bg-white/95 dark:bg-gray-800/95 backdrop-blur border border-gray-200 dark:border-gray-700 shadow-lg rounded-xl px-3 py-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-600 dark:text-gray-300">{{ __('Search') }}</span>
                    <span id="user-guide-floating-counter" class="text-xs font-medium text-gray-800 dark:text-gray-100 min-w-14 text-center"></span>
                    <button id="user-guide-floating-prev" type="button" class="btn btn-secondary px-3" disabled>
                        <iconify-icon icon="lucide:chevron-up"></iconify-icon>
                    </button>
                    <button id="user-guide-floating-next" type="button" class="btn btn-secondary px-3" disabled>
                        <iconify-icon icon="lucide:chevron-down"></iconify-icon>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <style>
                .markdown-body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif;
                    font-size: 16px;
                    line-height: 1.6;
                    word-wrap: break-word;
                    color: #111827;
                }
                .dark .markdown-body { color: #e5e7eb; }

                .markdown-body h1, .markdown-body h2, .markdown-body h3, .markdown-body h4 {
                    font-weight: 700;
                    line-height: 1.25;
                    margin-top: 1.2em;
                    margin-bottom: .6em;
                }
                .markdown-body h1 { font-size: 2em; padding-bottom: .3em; border-bottom: 1px solid #e5e7eb; }
                .markdown-body h2 { font-size: 1.5em; padding-bottom: .3em; border-bottom: 1px solid #e5e7eb; }
                .markdown-body h3 { font-size: 1.25em; }
                .markdown-body h4 { font-size: 1em; }
                .dark .markdown-body h1, .dark .markdown-body h2 { border-bottom-color: #374151; }

                .markdown-body p { margin: 0 0 16px; }

                .markdown-body a { color: #2563eb; text-decoration: none; }
                .markdown-body a:hover { text-decoration: underline; }
                .dark .markdown-body a { color: #93c5fd; }

                .markdown-body blockquote {
                    padding: 0 1em;
                    color: #4b5563;
                    border-left: .25em solid #d1d5db;
                    margin: 0 0 16px;
                }
                .dark .markdown-body blockquote { color: #d1d5db; border-left-color: #374151; }

                .markdown-body ul, .markdown-body ol { padding-left: 2em; margin: 0 0 16px; }
                .markdown-body li { margin: .25em 0; }

                .markdown-body code {
                    font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                    font-size: 85%;
                    background: rgba(175,184,193,.2);
                    padding: .15em .35em;
                    border-radius: 6px;
                }
                .dark .markdown-body code { background: rgba(148,163,184,.18); }

                .markdown-body pre {
                    margin: 0 0 16px;
                    padding: 16px;
                    overflow: auto;
                    font-size: 85%;
                    line-height: 1.45;
                    background: #0b1020;
                    color: #e5e7eb;
                    border-radius: 12px;
                }
                .markdown-body pre code { background: transparent; padding: 0; }

                .markdown-body table { border-collapse: collapse; width: 100%; margin: 0 0 16px; }
                .markdown-body table th, .markdown-body table td { border: 1px solid #d0d7de; padding: 6px 13px; }
                .markdown-body table tr { background: #fff; }
                .markdown-body table tr:nth-child(2n) { background: #f6f8fa; }
                .dark .markdown-body table th, .dark .markdown-body table td { border-color: #374151; }
                .dark .markdown-body table tr { background: transparent; }
                .dark .markdown-body table tr:nth-child(2n) { background: rgba(255,255,255,.03); }

                .markdown-body hr { border: none; border-top: 1px solid #e5e7eb; margin: 24px 0; }
                .dark .markdown-body hr { border-top-color: #374151; }
            </style>

            <div id="user-guide-content" class="markdown-body">
                {!! $html !!}
            </div>

            <div id="user-guide-no-results" class="hidden mt-4 text-sm text-gray-500 dark:text-gray-300">
                {{ __('No matching text found.') }}
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            (function () {
                const input = document.getElementById('user-guide-search');
                const content = document.getElementById('user-guide-content');
                const noResults = document.getElementById('user-guide-no-results');
                const prevBtn = document.getElementById('user-guide-prev');
                const nextBtn = document.getElementById('user-guide-next');
                const counter = document.getElementById('user-guide-counter');
                const floating = document.getElementById('user-guide-floating-nav');
                const floatingPrev = document.getElementById('user-guide-floating-prev');
                const floatingNext = document.getElementById('user-guide-floating-next');
                const floatingCounter = document.getElementById('user-guide-floating-counter');

                if (!input || !content) {
                    return;
                }

                const originalHtml = content.innerHTML;
                let debounceTimer = null;
                let matches = [];
                let currentIndex = -1;
                let lastRenderedQuery = '';

                function escapeRegExp(str) {
                    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                }

                function stripHighlights(html) {
                    return html.replace(/<mark class="user-guide-highlight">([\s\S]*?)<\/mark>/g, '$1');
                }

                function setActiveMatch(index) {
                    if (!matches.length) {
                        currentIndex = -1;
                        return;
                    }

                    const nextIndex = ((index % matches.length) + matches.length) % matches.length;
                    currentIndex = nextIndex;

                    matches.forEach((m) => {
                        m.style.outline = '';
                        m.style.outlineOffset = '';
                    });

                    const active = matches[currentIndex];
                    active.style.outline = '2px solid rgba(59,130,246,.65)';
                    active.style.outlineOffset = '2px';

                    active.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }

                function updateNav() {
                    const enabled = matches.length > 0;
                    if (prevBtn) prevBtn.disabled = !enabled;
                    if (nextBtn) nextBtn.disabled = !enabled;

                    if (floatingPrev) floatingPrev.disabled = !enabled;
                    if (floatingNext) floatingNext.disabled = !enabled;

                    if (counter) {
                        if (!enabled) {
                            counter.classList.add('hidden');
                            counter.textContent = '';
                        } else {
                            counter.classList.remove('hidden');
                            counter.textContent = (currentIndex + 1) + '/' + matches.length;
                        }
                    }

                    if (floatingCounter) {
                        floatingCounter.textContent = enabled ? ((currentIndex + 1) + '/' + matches.length) : '';
                    }

                    if (floating) {
                        floating.classList.toggle('hidden', !enabled);
                    }
                }

                function nextMatch() {
                    if (!matches.length) return;
                    setActiveMatch(currentIndex + 1);
                    updateNav();
                }

                function prevMatch() {
                    if (!matches.length) return;
                    setActiveMatch(currentIndex - 1);
                    updateNav();
                }

                function highlight(query) {
                    const q = (query || '').trim();
                    if (!q) {
                        content.innerHTML = originalHtml;
                        matches = [];
                        currentIndex = -1;
                        lastRenderedQuery = '';
                        if (noResults) noResults.classList.add('hidden');
                        updateNav();
                        return;
                    }

                    const safe = escapeRegExp(q);
                    const regex = new RegExp(safe, 'gi');

                    const raw = stripHighlights(originalHtml);
                    let matchCount = 0;
                    const next = raw.replace(regex, function (match) {
                        matchCount += 1;
                        return '<mark class="user-guide-highlight" style="background: rgba(59,130,246,.25); padding: 0 .1em; border-radius: .2em;">' + match + '</mark>';
                    });

                    content.innerHTML = next;
                    matches = Array.from(content.querySelectorAll('mark.user-guide-highlight'));
                    lastRenderedQuery = q;

                    const hasMatch = matchCount > 0;
                    if (noResults) {
                        noResults.classList.toggle('hidden', hasMatch);
                    }

                    if (hasMatch) {
                        requestAnimationFrame(() => {
                            setActiveMatch(0);
                            updateNav();
                        });
                    } else {
                        currentIndex = -1;
                        updateNav();
                    }
                }

                input.addEventListener('input', function (e) {
                    const value = e.target.value;
                    if (debounceTimer) {
                        clearTimeout(debounceTimer);
                    }

                    debounceTimer = setTimeout(() => {
                        highlight(value);
                    }, 120);
                });

                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (debounceTimer) {
                            clearTimeout(debounceTimer);
                        }
                        const q = (input.value || '').trim();
                        if (!q) {
                            highlight('');
                            return;
                        }

                        if (q === lastRenderedQuery && matches.length) {
                            if (e.shiftKey) {
                                prevMatch();
                            } else {
                                nextMatch();
                            }
                            return;
                        }

                        highlight(q);
                    }
                });

                if (prevBtn) {
                    prevBtn.addEventListener('click', function () {
                        prevMatch();
                    });
                }

                if (nextBtn) {
                    nextBtn.addEventListener('click', function () {
                        nextMatch();
                    });
                }

                if (floatingPrev) {
                    floatingPrev.addEventListener('click', function () {
                        prevMatch();
                    });
                }

                if (floatingNext) {
                    floatingNext.addEventListener('click', function () {
                        nextMatch();
                    });
                }
            })();

            function downloadUserGuide() {
                const button = document.getElementById('download-pdf-btn');
                const downloadText = document.getElementById('download-text');
                const originalText = downloadText.textContent;
                
                // Show loading state
                button.disabled = true;
                downloadText.textContent = '{{ __('Downloading...') }}';
                
                fetch('{{ route("admin.user-guide.download") }}')
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            try {
                                const data = JSON.parse(text);
                                throw new Error(data.error || 'Download failed');
                            } catch (jsonError) {
                                throw new Error(text || 'Download failed');
                            }
                        });
                    }
                    
                    return response.blob();
                })
                .then(blob => {
                    // Create download link
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    
                    // Get filename from Content-Disposition header or use default
                    const contentDisposition = response.headers.get('Content-Disposition');
                    let filename = 'AHC_User_Guide.md';
                    if (contentDisposition) {
                        const filenameMatch = contentDisposition.match(/filename="?([^"]+)"/);
                        if (filenameMatch) {
                            filename = filenameMatch[1];
                        }
                    }
                    
                    a.download = filename;
                    document.body.appendChild(a);
                    a.click();
                    window.URL.revokeObjectURL(url);
                    document.body.removeChild(a);
                    
                    // Reset button
                    button.disabled = false;
                    downloadText.textContent = originalText;
                })
                .catch(error => {
                    console.error('Download error:', error);
                    alert('Download failed: ' + error.message);
                    
                    // Reset button
                    button.disabled = false;
                    downloadText.textContent = originalText;
                });
        </script>
    @endpush
</x-layouts.backend-layout>
