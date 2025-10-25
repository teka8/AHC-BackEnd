<?php

declare(strict_types=1);

namespace App\Http\Requests\Page;

use App\Enums\Hooks\PageFilterHook;
use App\Enums\PageStatus;
use App\Http\Requests\FormRequest;
use App\Support\Facades\Hook;
use Illuminate\Support\Str;

class UpdatePageRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $pageId = $this->page;
        $pageStatuses = implode(',', array_map(fn($status) => $status->value, PageStatus::cases()));

        return Hook::applyFilters(PageFilterHook::PAGE_UPDATE_VALIDATION_RULES, [
            /** @example "Updated: Laravel Development Best Practices" */
            'title' => 'required|string|max:255',

            /** @example "laravel-development-best-practices" */
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $pageId,

            /** @example "<p>In this updated guide, we explore the best practices for Laravel development...</p>" */
            'content' => 'nullable|string',

            /** @example "Discover the latest best practices for Laravel application development." */
            'excerpt' => 'nullable|string',

            /** @example "published" */
            'status' => 'string',

            /** @example null */
            'published_at' => 'nullable|date',

        ], $pageId);
    }
}