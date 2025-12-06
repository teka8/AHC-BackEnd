<?php

use App\Http\Controllers\Api\ActionLogController;
use App\Http\Controllers\Api\AiContentController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ModuleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TermController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Backend\Api\TermController as BackendTermController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmailSubscriptionController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\PublicResourceController;
use App\Http\Controllers\Api\AhcLeaderController;
use App\Http\Controllers\Api\ContactMessageController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public API endpoints
Route::get('/translations/{lang}', function (string $lang) {
    $path = resource_path("lang/{$lang}.json");

    if (! file_exists($path)) {
        return response()->json(['error' => 'Language not found'], 404);
    }

    $translations = json_decode(file_get_contents($path), true);

    return response()->json($translations);
});



// Authentication routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/revoke-all', [AuthController::class, 'revokeAll']);
    });
});

// Protected API routes
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // User management
    Route::apiResource('users', UserController::class);
    Route::post('users/bulk-delete', [UserController::class, 'bulkDelete'])->name('api.users.bulk-delete');

    // Role management
    Route::apiResource('roles', RoleController::class);
    Route::post('roles/delete/bulk-delete', [RoleController::class, 'bulkDelete'])->name('api.roles.bulk-delete');

    // Permission management
    Route::get('permissions', [PermissionController::class, 'index'])->name('api.permissions.index');
    Route::get('permissions/groups', [PermissionController::class, 'groups'])->name('api.permissions.groups');
    Route::get('permissions/{id}', [PermissionController::class, 'show'])->name('api.permissions.show');

    // Posts management (dynamic post types)
    Route::prefix('posts')->group(function () {
        Route::get('/{postType?}', [PostController::class, 'index'])->name('api.posts.index');
        Route::post('/{postType}', [PostController::class, 'store'])->name('api.posts.store');
        Route::get('/{postType}/{id}', [PostController::class, 'show'])->name('api.posts.show');
        Route::put('/{postType}/{id}', [PostController::class, 'update'])->name('api.posts.update');
        Route::delete('/{postType}/{id}', [PostController::class, 'destroy'])->name('api.posts.destroy');
        Route::post('/{postType}/bulk-delete', [PostController::class, 'bulkDelete'])->name('api.posts.bulk-delete');
    });

    // Terms management (Categories, Tags, etc.)
    Route::prefix('terms')->group(function () {
        Route::get('/{taxonomy}', [TermController::class, 'index'])->name('api.terms.index');
        Route::post('/{taxonomy}', [TermController::class, 'store'])->name('api.terms.store');
        Route::get('/{taxonomy}/{id}', [TermController::class, 'show'])->name('api.terms.show');
        Route::put('/{taxonomy}/{id}', [TermController::class, 'update'])->name('api.terms.update');
        Route::delete('/{taxonomy}/{id}', [TermController::class, 'destroy'])->name('api.terms.destroy');
        Route::post('/{taxonomy}/bulk-delete', [TermController::class, 'bulkDelete'])->name('api.terms.bulk-delete');
    });

    // Settings management
    Route::get('settings', [SettingController::class, 'index'])->name('api.settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('api.settings.update');
    Route::get('settings/{key}', [SettingController::class, 'show'])->name('api.settings.show');

    // Action logs
    Route::get('action-logs', [ActionLogController::class, 'index'])->name('api.action-logs.index');
    Route::get('action-logs/{id}', [ActionLogController::class, 'show'])->name('api.action-logs.show');

    // AI Content Generation
    Route::prefix('ai')->group(function () {
        Route::get('providers', [AiContentController::class, 'getProviders'])->name('api.ai.providers');
        Route::post('generate-content', [AiContentController::class, 'generateContent'])->name('api.ai.generate-content');
    });

    // Module management
    Route::get('modules', [ModuleController::class, 'index'])->name('api.modules.index');
    Route::get('modules/{name}', [ModuleController::class, 'show'])->name('api.modules.show');
    Route::patch('modules/{name}/toggle-status', [ModuleController::class, 'toggleStatus'])->name('api.modules.toggle-status');
    Route::delete('modules/{name}', [ModuleController::class, 'destroy'])->name('api.modules.destroy');
});

// Admin API routes (for backward compatibility with existing web-based API calls)
Route::middleware(['auth', 'web'])->prefix('admin')->name('admin.api.')->group(function () {
    // Terms API (existing)
    Route::post('/terms/{taxonomy}', [BackendTermController::class, 'store'])->name('terms.store');
    Route::put('/terms/{taxonomy}/{id}', [BackendTermController::class, 'update'])->name('terms.update');
    Route::delete('/terms/{taxonomy}/{id}', [BackendTermController::class, 'destroy'])->name('terms.destroy');
});

Route::middleware('api')->prefix('v1')->group(function () {
    Route::post('/subscriptions', [EmailSubscriptionController::class, 'store'])->name('api.subscriptions.store');
    Route::post('/subscriptions/unsubscribe', [EmailSubscriptionController::class, 'unsubscribe'])->name('api.subscriptions.unsubscribe');

    // Contact messages
    Route::post('/contact', [ContactMessageController::class, 'store'])->name('api.contact.store');

    // Public page routes
    Route::get('/pages', [PageController::class, 'index']);
    Route::get('/pages/{id}', [PageController::class, 'show']);
    Route::get('/pages/slug/{slug}', [PageController::class, 'showBySlug']);
    Route::get('/pages/section/{section}', [PageController::class, 'bySection']);
    Route::get('/navigation', [PageController::class, 'navigation']);
    Route::get('/footer', [PageController::class, 'footer']);

    // Events management
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{id}', [EventController::class, 'show']);

    // Health Innovation & Entrepreneurship
    Route::prefix('health-innovation')->group(function () {
        // Ventures (public)
        Route::get('/ventures', [\App\Http\Controllers\Api\HealthInnovation\VentureController::class, 'index']);
        Route::get('/ventures/{id}', [\App\Http\Controllers\Api\HealthInnovation\VentureController::class, 'show']);
        Route::post('/ventures/{id}/vote', [\App\Http\Controllers\Api\HealthInnovation\VentureController::class, 'vote']);

        // Venture Updates (public)
        Route::get('/updates', [\App\Http\Controllers\Api\HealthInnovation\VentureUpdateController::class, 'index']);

        // Applications (requires auth)
        // Route::middleware('auth:sanctum')->group(function () {
        Route::post('/applications', [\App\Http\Controllers\Api\HealthInnovation\VentureApplicationController::class, 'store']);
        Route::post('/applications/draft', [\App\Http\Controllers\Api\HealthInnovation\VentureApplicationController::class, 'saveDraft']);
        Route::patch('/applications/{id}/draft', [\App\Http\Controllers\Api\HealthInnovation\VentureApplicationController::class, 'saveDraft']);
        Route::get('/applications/my', [\App\Http\Controllers\Api\HealthInnovation\VentureApplicationController::class, 'myApplications']);
        Route::get('/applications/{id}', [\App\Http\Controllers\Api\HealthInnovation\VentureApplicationController::class, 'show']);
        // });
    });

    // Scholarship Portal
    Route::prefix('scholarships')->group(function () {
        // Scholarships (public)
        Route::get('/', [\App\Http\Controllers\Api\Scholarship\ScholarshipController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\Api\Scholarship\ScholarshipController::class, 'show']);

        // Applications (requires auth)
        // Route::middleware('auth:sanctum')->group(function () {
        Route::post('/applications', [\App\Http\Controllers\Api\Scholarship\ScholarshipApplicationController::class, 'store']);
        Route::post('/applications/draft', [\App\Http\Controllers\Api\Scholarship\ScholarshipApplicationController::class, 'saveDraft']);
        Route::patch('/applications/{id}/draft', [\App\Http\Controllers\Api\Scholarship\ScholarshipApplicationController::class, 'saveDraft']);
        Route::get('/applications/my', [\App\Http\Controllers\Api\Scholarship\ScholarshipApplicationController::class, 'myApplications']);
        Route::get('/applications/{id}', [\App\Http\Controllers\Api\Scholarship\ScholarshipApplicationController::class, 'show']);
        Route::get('/applications/{id}/status', [\App\Http\Controllers\Api\Scholarship\ScholarshipApplicationController::class, 'statusHistory']);

        // Admin routes (add role check middleware as needed)
        Route::get('/admin/applications', [\App\Http\Controllers\Api\Scholarship\ScholarshipApplicationController::class, 'adminIndex']);
        Route::patch('/applications/{id}/status', [\App\Http\Controllers\Api\Scholarship\ScholarshipApplicationController::class, 'updateStatus']);
        // });
    });

    // Public posts (news)
    Route::prefix('public')->group(function () {
        // Chatbot route
        Route::post('/chat', [App\Http\Controllers\Api\ChatController::class, 'chat']);

        Route::get('/posts', [\App\Http\Controllers\Api\PublicPostController::class, 'index']);
        Route::get('/posts/{id}', [\App\Http\Controllers\Api\PublicPostController::class, 'show']);

        // Public media
        Route::get('/media', [\App\Http\Controllers\Api\PublicMediaController::class, 'index']);

        // Public programs
        Route::get('/programs', [\App\Http\Controllers\Api\ProgramController::class, 'index']);
        Route::get('/programs/{program}', [\App\Http\Controllers\Api\ProgramController::class, 'show']);

        // Public resources
        Route::prefix('resources')->group(function () {
            // Categories routes first (no parameters)
            Route::get('/documents/categories', [PublicResourceController::class, 'documentCategories']);
            Route::get('/educational/categories', [PublicResourceController::class, 'educationalCategories']);
            Route::get('/others/categories', [PublicResourceController::class, 'othersCategories']);

            // Download tracking endpoints (with numeric constraint)
            Route::post('/documents/{id}/download', [PublicResourceController::class, 'documentDownload'])->where('id', '[0-9]+');
            Route::post('/educational/{id}/download', [PublicResourceController::class, 'educationalDownload'])->where('id', '[0-9]+');
            Route::post('/others/{id}/download', [PublicResourceController::class, 'othersDownload'])->where('id', '[0-9]+');
            
            // GET download route for direct file download (used in email links)
            Route::get('/others/{id}/file', [PublicResourceController::class, 'othersFileDownload'])->where('id', '[0-9]+')->name('api.resources.others.file');

            // List endpoints (must come before show endpoints)
            Route::get('/documents', [PublicResourceController::class, 'documents']);
            Route::get('/educational', [PublicResourceController::class, 'educational']);
            Route::get('/others', [PublicResourceController::class, 'others']);

            // Show endpoints with numeric constraint (must come last)
            Route::get('/documents/{id}', [PublicResourceController::class, 'documentsShow'])->where('id', '[0-9]+');
            Route::get('/educational/{id}', [PublicResourceController::class, 'educationalShow'])->where('id', '[0-9]+');
            Route::get('/others/{id}', [PublicResourceController::class, 'othersShow'])->where('id', '[0-9]+');
        });

        // AHC Leaders
        Route::get('/ahc-leaders', [AhcLeaderController::class, 'index']);
        Route::get('/ahc-leaders/{ahcLeader}', [AhcLeaderController::class, 'show']);

        // Company Info
        Route::get('/company-info', [SettingController::class, 'publicCompanyInfo']);
    });
});
