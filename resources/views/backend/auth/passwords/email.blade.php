@extends('backend.auth.layouts.app')

@section('title')
    {{ __('Forgot Password') }} | {{ config('app.name') }}
@endsection

@push('styles')
<style>
.forgot-container { animation: fadeInUp 0.6s ease-out; }
.card { transition: all 0.3s ease; }
.card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
.space-y-5 > * { animation: slideInLeft 0.5s ease-out; animation-fill-mode: both; }
.space-y-5 > *:nth-child(1) { animation-delay: 0.1s; }
.space-y-5 > *:nth-child(2) { animation-delay: 0.2s; }
.space-y-5 > *:nth-child(3) { animation-delay: 0.3s; }
.space-y-5 > *:nth-child(4) { animation-delay: 0.4s; }
.btn-primary, .btn { transition: all 0.3s ease; }
.btn-primary:hover, .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.2); }
.form-control { transition: all 0.3s ease; }
.form-control:focus { transform: scale(1.02); }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
@keyframes slideInLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
</style>
@endpush

@section('admin-content')
<div class="container mx-auto px-4">
    <div class="flex justify-center">
        <div class="w-full max-w-md forgot-container">
            <div class="card bg-white dark:bg-gray-800 shadow-lg rounded-lg">
                <div class="card-header bg-gray-50 dark:bg-gray-700 px-6 py-4 border-b border-gray-200 dark:border-gray-600">
                    <h1 class="mb-0 font-semibold text-gray-700 dark:text-white text-lg">
                        {{ __('Forgot Password') }}
                    </h1>
                </div>
                <div class="card-body p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-300 mb-6">
                        {{ __('Enter your email address and we will send you a link to reset your password.') }}
                    </p>
                    <form action="{{ route('admin.password.email') }}" method="POST">
                        @csrf
                        <div class="space-y-5">
                            <x-messages />
                            <!-- Email -->
                            <div>
                                <label class="form-label">
                                    {{ __('Email') }}<span class="text-error-500">*</span>
                                </label>
                                <input autofocus type="text" id="email" name="email" autocomplete="username"
                                    placeholder="{{ __('Enter your email address') }}"
                                    class="form-control">
                            </div>
                            
                            <x-recaptcha page="forgot_password" />

                            <div>
                                <button type="submit" class="btn-primary w-full">
                                    {{ __('Send Reset Link') }}
                                    <iconify-icon icon="lucide:log-in" class="ml-2"></iconify-icon>
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="flex justify-center items-center mt-5 text-sm font-normal text-center text-gray-700 dark:text-gray-300">
                        <a href="{{ route('admin.login') }}" class="btn text-primary">
                            <iconify-icon icon="lucide:chevron-left" class="mr-2"></iconify-icon>
                            {{ __('Back to Login') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

