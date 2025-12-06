<div class="space-y-6">
    <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
        <div class="sm:col-span-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('Contact Information') }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Enter the company contact details to be displayed on the website.') }}
            </p>
        </div>

        <!-- Company Name -->
        <div class="sm:col-span-3">
            <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Company Name') }}
            </label>
            <div class="mt-1">
                <input type="text" name="company_name" id="company_name"
                    value="{{ old('company_name', config('company_name')) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
        </div>

        <!-- Company Email -->
        <div class="sm:col-span-3">
            <label for="company_email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Company Email') }}
            </label>
            <div class="mt-1">
                <input type="email" name="company_email" id="company_email"
                    value="{{ old('company_email', config('company_email')) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
        </div>

        <!-- Company Phone -->
        <div class="sm:col-span-3">
            <label for="company_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Company Phone') }}
            </label>
            <div class="mt-1">
                <input type="text" name="company_phone" id="company_phone"
                    value="{{ old('company_phone', config('company_phone')) }}"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
        </div>

        <!-- Company Address -->
        <div class="sm:col-span-6">
            <label for="company_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Company Address') }}
            </label>
            <div class="mt-1">
                <textarea name="company_address" id="company_address" rows="3"
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('company_address', config('company_address')) }}</textarea>
            </div>
        </div>

        <div class="sm:col-span-6 pt-6">
            <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white">{{ __('Social Media Links') }}</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Enter the full URL for your social media profiles.') }}
            </p>
        </div>

        <!-- Facebook -->
        <div class="sm:col-span-3">
            <label for="social_facebook" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Facebook URL') }}
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </span>
                </div>
                <input type="url" name="social_facebook" id="social_facebook"
                    value="{{ old('social_facebook', config('social_facebook')) }}"
                    class="block w-full rounded-md border-gray-300 pl-10 focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="https://facebook.com/yourpage">
            </div>
        </div>

        <!-- Twitter / X -->
        <div class="sm:col-span-3">
            <label for="social_twitter" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Twitter / X URL') }}
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </span>
                </div>
                <input type="url" name="social_twitter" id="social_twitter"
                    value="{{ old('social_twitter', config('social_twitter')) }}"
                    class="block w-full rounded-md border-gray-300 pl-10 focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="https://twitter.com/yourhandle">
            </div>
        </div>

        <!-- LinkedIn -->
        <div class="sm:col-span-3">
            <label for="social_linkedin" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('LinkedIn URL') }}
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </span>
                </div>
                <input type="url" name="social_linkedin" id="social_linkedin"
                    value="{{ old('social_linkedin', config('social_linkedin')) }}"
                    class="block w-full rounded-md border-gray-300 pl-10 focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="https://linkedin.com/company/yourcompany">
            </div>
        </div>

        <!-- Instagram -->
        <div class="sm:col-span-3">
            <label for="social_instagram" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('Instagram URL') }}
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.069-4.85.069-3.204 0-3.584-.012-4.849-.069-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                        </svg>
                    </span>
                </div>
                <input type="url" name="social_instagram" id="social_instagram"
                    value="{{ old('social_instagram', config('social_instagram')) }}"
                    class="block w-full rounded-md border-gray-300 pl-10 focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="https://instagram.com/yourprofile">
            </div>
        </div>

        <!-- YouTube -->
        <div class="sm:col-span-3">
            <label for="social_youtube" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('YouTube URL') }}
            </label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <span class="text-gray-500 sm:text-sm">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </span>
                </div>
                <input type="url" name="social_youtube" id="social_youtube"
                    value="{{ old('social_youtube', config('social_youtube')) }}"
                    class="block w-full rounded-md border-gray-300 pl-10 focus:border-primary-500 focus:ring-primary-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="https://youtube.com/yourchannel">
            </div>
        </div>
    </div>
</div>
