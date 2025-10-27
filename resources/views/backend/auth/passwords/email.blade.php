@extends('backend.auth.layouts.app')

@section('title')
    {{ __('Forgot Password') }} | {{ config('app.name') }}
@endsection

@push('styles')
<style>
.forgot-container { animation: fadeInUp 0.6s ease-out; }
.space-y-6 > * { animation: slideInLeft 0.5s ease-out; animation-fill-mode: both; }
.space-y-6 > *:nth-child(1) { animation-delay: 0.1s; }
.space-y-6 > *:nth-child(2) { animation-delay: 0.2s; }
.space-y-6 > *:nth-child(3) { animation-delay: 0.3s; }
.space-y-6 > *:nth-child(4) { animation-delay: 0.4s; }
.space-y-6 > *:nth-child(5) { animation-delay: 0.5s; }
.btn-primary, .btn { transition: all 0.3s ease; }
.btn-primary:hover, .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
.form-control { transition: all 0.3s ease; }
.form-control:focus { transform: scale(1.02); }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
</style>
@endpush

@section('admin-content')
<x-card class="login-card mx-auto shadow-lg p-8 forgot-container">
    <div class="text-center mb-8">
        <img src="{{ config('settings.site_logo_lite') ? asset(config('settings.site_logo_lite')) : asset('images/logo/african healt.jpg') }}" alt="{{ config('app.name') }}" class="h-16 mx-auto mb-6">
        <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-2">
            {{ __('Forgot Password') }}
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            {{ __('Enter your email address and we will send you a link to reset your password.') }}
        </p>
    </div>
    <div>
        <form action="{{ route('admin.password.email') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <x-messages />
                
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300" for="email">
                        {{ __('Email Address') }}
                    </label>
                    <input autofocus type="text" id="email" name="email" autocomplete="username"
                        placeholder="{{ __('Enter your email address') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        required>
                </div>
                
                <x-recaptcha page="forgot_password" />

                <div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200 flex items-center justify-center">
                        {{ __('Send Reset Link') }}
                        <iconify-icon icon="lucide:mail" class="ml-2"></iconify-icon>
                    </button>
                </div>
                
                <div class="text-center">
                    <a href="{{ route('admin.login') }}" class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400 inline-flex items-center">
                        <iconify-icon icon="lucide:chevron-left" class="mr-2"></iconify-icon>
                        {{ __('Back to Login') }}
                    </a>
                </div>
            </div>
        </form>
    </div>
</x-card>
@endsection

