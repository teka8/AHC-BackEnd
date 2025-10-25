<?php

declare(strict_types=1);

namespace App\Http\Requests\Page;

use App\Enums\Hooks\PageFilterHook;
use App\Http\Requests\FormRequest;
use App\Support\Facades\Hook;
use Illuminate\Support\Str;

class StorePageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled by the controller using policies.
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize meta keys by slugifying them
        if ($this->has('meta_keys')) {
            $metaKeys = $this->input('meta_keys', []);
            // Ensure $metaKeys is always an array
            $metaKeys = is_array($metaKeys) ? $metaKeys : [];
            $sanitizedKeys = array_map(function ($key) {
                return ! empty($key) ? Str::slug($key, '_') : $key;
            }, $metaKeys);

            $this->merge([
                'meta_keys' => $sanitizedKeys,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $pageStatuses = implode(',', array_map(fn ($status) => $status->value, \App\Enums\PageStatus::cases()));

        return Hook::applyFilters(PageFilterHook::PAGE_STORE_VALIDATION_RULES, [
            /** @example "How to Build a Laravel Application" */
            'title' => 'required|string|max:255',

            /** @example "how-to-build-a-laravel-application" */
            'slug' => 'nullable|string|max:255|unique:pages',

            /** @example "<p>This is a comprehensive guide to building Laravel applications...</p>" */
            'content' => 'nullable|string',

            /** @example "Learn the fundamentals of building Laravel applications from scratch." */
            'excerpt' => 'nullable|string',

            /** @example "publish" */
            // 'status' => 'required|in:' . $pageStatuses,

            /** @example null */
            'published_at' => 'nullable|date',

            
        ]);
    }
}
