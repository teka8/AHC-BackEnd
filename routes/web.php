<?php

declare(strict_types=1);

use App\Http\Controllers\Backend\ActionLogController;
use App\Http\Controllers\Backend\Auth\ScreenshotGeneratorLoginController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\LocaleController;
use App\Http\Controllers\Backend\MediaController;
use App\Http\Controllers\Backend\DocumentRepositoryController;
use App\Http\Controllers\Backend\EducationRepositoryController;
use App\Http\Controllers\Backend\ModuleController;
use App\Http\Controllers\Backend\OthersController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\PostController;
use App\Http\Controllers\Backend\ProfileController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SettingController;
use App\Http\Controllers\Backend\TermController;
use App\Http\Controllers\Backend\TranslationController;
use App\Http\Controllers\Backend\UserLoginAsController;
use App\Http\Controllers\Backend\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\EventController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/**
 * Admin routes.
 */
Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => ['auth']], function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('roles', RoleController::class);
    Route::delete('roles/delete/bulk-delete', [RoleController::class, 'bulkDelete'])->name('roles.bulk-delete');

    // Permissions Routes.
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::patch('/permissions/{permission}/toggle-status', [PermissionController::class, 'toggleStatus'])->name('permissions.toggle-status');

    // Modules Routes.
    Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
    Route::post('/modules/toggle-status/{module}', [ModuleController::class, 'toggleStatus'])->name('modules.toggle-status');
    Route::post('/modules/upload', [ModuleController::class, 'store'])->name('modules.store');
    Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.delete');

    // Settings Routes.
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'store'])->name('settings.store');

    // Translation Routes.
    Route::get('/translations', [TranslationController::class, 'index'])->name('translations.index');
    Route::post('/translations', [TranslationController::class, 'update'])->name('translations.update');
    Route::post('/translations/create', [TranslationController::class, 'create'])->name('translations.create');

    // Login as & Switch back.
    Route::resource('users', UserController::class);
    Route::delete('users/delete/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
    Route::get('users/{id}/login-as', [UserLoginAsController::class, 'loginAs'])->name('users.login-as');
    Route::post('users/switch-back', [UserLoginAsController::class, 'switchBack'])->name('users.switch-back');

    // Action Log Routes.
    Route::get('/action-log', [ActionLogController::class, 'index'])->name('actionlog.index');
    
    // Notification Routes.
    Route::get('/notifications', [App\Http\Controllers\Backend\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Backend\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/read/{id}', [App\Http\Controllers\Backend\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // Posts/Pages Routes - Dynamic post types.
    Route::get('/posts/{postType?}', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/{postType}/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts/{postType}', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{postType}/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{postType}/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{postType}/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::patch('/posts/{postType}/{post}/status', [PostController::class, 'updateStatus'])->name('posts.update-status');
    Route::delete('/posts/{postType}/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::delete('/posts/{postType}/delete/bulk-delete', [PostController::class, 'bulkDelete'])->name('posts.bulk-delete');

    // Event Routes.
    Route::get('events', [EventController::class, 'index'])->name('events.index');
    Route::get('events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::delete('/events/delete/bulk-delete', [EventController::class, 'bulkDelete'])->name('events.bulk-delete');




    // Terms Routes (Categories, Tags, etc.).
    Route::get('/terms/{taxonomy}', [TermController::class, 'index'])->name('terms.index');
    Route::get('/terms/{taxonomy}/{term}/edit', [TermController::class, 'edit'])->name('terms.edit');
    Route::post('/terms/{taxonomy}', [TermController::class, 'store'])->name('terms.store');
    Route::put('/terms/{taxonomy}/{term}', [TermController::class, 'update'])->name('terms.update');
    Route::delete('/terms/{taxonomy}/{term}', [TermController::class, 'destroy'])->name('terms.destroy');
    Route::delete('/terms/{taxonomy}/delete/bulk-delete', [TermController::class, 'bulkDelete'])->name('terms.bulk-delete');

    // Media Routes.
    Route::prefix('media')->name('media.')->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('index');
        Route::get('/api', [MediaController::class, 'api'])->name('api');
        Route::post('/', [MediaController::class, 'store'])->name('store')->middleware('check.upload.limits');
        Route::get('/upload-limits', [MediaController::class, 'getUploadLimits'])->name('upload-limits');
        Route::delete('/{id}', [MediaController::class, 'destroy'])->name('destroy');
        Route::delete('/', [MediaController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Media Routes.
    Route::prefix('document')->name('document.')->group(function () {
        Route::get('/', [DocumentRepositoryController::class, 'index'])->name('index');
        Route::get('/api', [DocumentRepositoryController::class, 'api'])->name('api');
        Route::post('/', [DocumentRepositoryController::class, 'store'])->name('store')->middleware('check.upload.limits');
        Route::get('/upload-limits', [DocumentRepositoryController::class, 'getUploadLimits'])->name('upload-limits');

        // Additional routes you might want to add:
        Route::get('/{id}/edit', [DocumentRepositoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DocumentRepositoryController::class, 'update'])->name('update');
        Route::post('/{id}/publish', [DocumentRepositoryController::class, 'publish'])->name('publish');
        Route::post('/{id}/approve', [DocumentRepositoryController::class, 'approve'])->name('approve');

        Route::get('/{id}/edit', [DocumentRepositoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [DocumentRepositoryController::class, 'update'])->name('update');
        Route::post('/{id}/publish', [DocumentRepositoryController::class, 'publish'])->name('publish');
        Route::post('/{id}/approve', [DocumentRepositoryController::class, 'approve'])->name('approve');

        Route::delete('/{id}', [DocumentRepositoryController::class, 'destroy'])->name('destroy');
        Route::delete('/', [DocumentRepositoryController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/{id}/restore', [DocumentRepositoryController::class, 'restore'])->name('restore');
        Route::delete('/{id}/force', [DocumentRepositoryController::class, 'forceDelete'])->name('force-delete');
    
        // Download routes
        Route::get('/{id}/download', [DocumentRepositoryController::class, 'download'])->name('download');
        Route::get('/{id}/preview', [DocumentRepositoryController::class, 'preview'])->name('preview');
        Route::get('/{id}/stats', [DocumentRepositoryController::class, 'downloadStats'])->name('stats');
        Route::post('/{id}/increment-download', [DocumentRepositoryController::class, 'incrementDownload'])->name('increment-download');
    });

    Route::prefix('education')->name('education.')->group(function () {
        Route::get('/', [EducationRepositoryController::class, 'index'])->name('index');
        Route::get('/api', [EducationRepositoryController::class, 'api'])->name('api');
        Route::post('/', [EducationRepositoryController::class, 'store'])->name('store')->middleware('check.upload.limits');
        Route::get('/upload-limits', [EducationRepositoryController::class, 'getUploadLimits'])->name('upload-limits');
        Route::get('/{id}/edit', [EducationRepositoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [EducationRepositoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [EducationRepositoryController::class, 'destroy'])->name('destroy');
        Route::delete('/', [EducationRepositoryController::class, 'bulkDelete'])->name('bulk-delete');

        // Download routes
        Route::get('/{id}/download', [EducationRepositoryController::class, 'download'])->name('download');
        Route::get('/{id}/preview', [EducationRepositoryController::class, 'preview'])->name('preview');
        Route::get('/{id}/stats', [EducationRepositoryController::class, 'downloadStats'])->name('stats');
        Route::post('/{id}/increment-download', [EducationRepositoryController::class, 'incrementDownload'])->name('increment-download');
    });

    //Route for others
    Route::prefix('others')->name('others.')->group(function () {
        Route::get('/', [OthersController::class, 'index'])->name('index');
        Route::get('/api', [OthersController::class, 'api'])->name('api');
        Route::post('/', [OthersController::class, 'store'])->name('store')->middleware('check.upload.limits');
        Route::get('/upload-limits', [OthersController::class, 'getUploadLimits'])->name('upload-limits');
        Route::get('/{id}/edit', [OthersController::class, 'edit'])->name('edit');
        Route::put('/{id}', [OthersController::class, 'update'])->name('update');
        Route::delete('/{id}', [OthersController::class, 'destroy'])->name('destroy');
        Route::delete('/', [OthersController::class, 'bulkDelete'])->name('bulk-delete');

        // Download routes
        Route::get('/{id}/download', [OthersController::class, 'download'])->name('download');
        Route::get('/{id}/preview', [OthersController::class, 'preview'])->name('preview');
        Route::get('/{id}/stats', [OthersController::class, 'downloadStats'])->name('stats');
        Route::post('/{id}/increment-download', [OthersController::class, 'incrementDownload'])->name('increment-download');
    });


    // Editor Upload Route.
    Route::post('/editor/upload', [App\Http\Controllers\Backend\EditorController::class, 'upload'])->name('editor.upload');

    // AI Content Generation Routes.
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/providers', [App\Http\Controllers\Backend\AiContentController::class, 'getProviders'])->name('providers');
        Route::post('/generate-content', [App\Http\Controllers\Backend\AiContentController::class, 'generateContent'])->name('generate-content');
    });
});

/**
 * Profile routes.
 */
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'middleware' => ['auth']], function () {
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::put('/update-additional', [ProfileController::class, 'updateAdditional'])->name('update.additional');
});

Route::get('/locale/{lang}', [LocaleController::class, 'switch'])->name('locale.switch');
Route::get('/screenshot-login/{email}', [ScreenshotGeneratorLoginController::class, 'login'])->middleware('web')->name('screenshot.login');
Route::get('/demo-preview', fn() => view('demo.preview'))->name('demo.preview');

