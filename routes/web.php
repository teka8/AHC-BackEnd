<?php

declare(strict_types=1);

use App\Http\Controllers\Backend\ActionLogController;
use App\Http\Controllers\Backend\Auth\ScreenshotGeneratorLoginController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\LocaleController;
use App\Http\Controllers\Backend\MediaController;
use App\Http\Controllers\Backend\MediaManagerController;
use App\Http\Controllers\Backend\DocumentRepositoryController;
use App\Http\Controllers\Backend\EducationRepositoryController;
use App\Http\Controllers\Backend\EmailSubscriptionController;
use App\Http\Controllers\Backend\ContactMessageController;
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
use App\Http\Controllers\Backend\PageController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

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
    // Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
    // Route::post('/modules/toggle-status/{module}', [ModuleController::class, 'toggleStatus'])->name('modules.toggle-status');
    // Route::post('/modules/upload', [ModuleController::class, 'store'])->name('modules.store');
    // Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.delete');

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

    Route::post('/news/{id}/change-status', [PostController::class, 'changeStatus'])->name('news.change-status');
    Route::post('/announcements/{id}/change-status', [PostController::class, 'changeStatus'])->name('announcements.change-status');
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

    // Pages Routes
    Route::get('pages', [PageController::class, 'index'])->name('pages.index');
    Route::get('pages/create', [PageController::class, 'create'])->name('pages.create');
    Route::post('pages', [PageController::class, 'store'])->name('pages.store');
    Route::put('pages/{page}/status', [PageController::class, 'changeStatus'])->name('status');
    Route::get('/pages/{page}', [PageController::class, 'show'])->name('pages.show');
    Route::get('/pages/{page}/edit', [PageController::class, 'edit'])->name('pages.edit');
    Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');
    Route::delete('/pages/delete/bulk-delete', [PageController::class, 'bulkDelete'])->name('pages.bulk-delete');

    // Event Routes.
    Route::get('events', [EventController::class, 'index'])->name('events.index');
    Route::get('events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::get('/events/{event}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::delete('/events/delete/bulk-delete', [EventController::class, 'bulkDelete'])->name('events.bulk-delete');
    Route::post('events/{id}/change-status', [EventController::class, 'changeStatus'])->name('events.change-status');

    // AHC Leaders Routes
    Route::prefix('ahc-leaders')->name('ahc-leaders.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Backend\AhcLeaderController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Backend\AhcLeaderController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Backend\AhcLeaderController::class, 'store'])->name('store');
        Route::get('/{ahcLeader}/edit', [\App\Http\Controllers\Backend\AhcLeaderController::class, 'edit'])->name('edit');
        Route::put('/{ahcLeader}', [\App\Http\Controllers\Backend\AhcLeaderController::class, 'update'])->name('update');
        Route::get('/{ahcLeader}', [\App\Http\Controllers\Backend\AhcLeaderController::class, 'show'])->name('show');
        Route::delete('/{ahcLeader}', [\App\Http\Controllers\Backend\AhcLeaderController::class, 'destroy'])->name('destroy');
        Route::delete('/delete/bulk-delete', [\App\Http\Controllers\Backend\AhcLeaderController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Contact Messages Routes
    Route::prefix('contact-messages')->name('contact-messages.')->group(function () {
        Route::get('/', [ContactMessageController::class, 'index'])->name('index');
        Route::get('/{contactMessage}', [ContactMessageController::class, 'show'])->name('show');
        Route::patch('/{contactMessage}/toggle-replied', [ContactMessageController::class, 'toggleReplied'])->name('toggle-replied');
        Route::delete('/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('destroy');
    });

    // Frontend Analytics Routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Backend\AnalyticsController::class, 'index'])->name('index');
        Route::get('/realtime', [\App\Http\Controllers\Backend\AnalyticsController::class, 'getRealTime'])->name('realtime');
        Route::get('/overview', [\App\Http\Controllers\Backend\AnalyticsController::class, 'getOverview'])->name('overview');
        Route::get('/users-trend', [\App\Http\Controllers\Backend\AnalyticsController::class, 'getUsersTrend'])->name('users-trend');
        Route::get('/top-pages', [\App\Http\Controllers\Backend\AnalyticsController::class, 'getTopPages'])->name('top-pages');
        Route::get('/top-events', [\App\Http\Controllers\Backend\AnalyticsController::class, 'getTopEvents'])->name('top-events');
        Route::get('/traffic-sources', [\App\Http\Controllers\Backend\AnalyticsController::class, 'getTrafficSources'])->name('traffic-sources');
        Route::get('/geography', [\App\Http\Controllers\Backend\AnalyticsController::class, 'getGeography'])->name('geography');
        Route::get('/devices', [\App\Http\Controllers\Backend\AnalyticsController::class, 'getDevices'])->name('devices');
        Route::get('/browsers', [\App\Http\Controllers\Backend\AnalyticsController::class, 'getBrowsers'])->name('browsers');
        Route::get('/operating-systems', [\App\Http\Controllers\Backend\AnalyticsController::class, 'getOperatingSystems'])->name('operating-systems');
        Route::get('/landing-pages', [\App\Http\Controllers\Backend\AnalyticsController::class, 'getLandingPages'])->name('landing-pages');
    });

    Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
        Route::get('/', [EmailSubscriptionController::class, 'index'])->name('index');
        Route::post('/bulk-export', [EmailSubscriptionController::class, 'export'])->name('bulk-export');
        Route::patch('/{subscription}/unsubscribe', [EmailSubscriptionController::class, 'unsubscribe'])
            ->name('unsubscribe');
        Route::patch('/{subscription}/resubscribe', [EmailSubscriptionController::class, 'resubscribe'])
            ->name('resubscribe');
        Route::delete('/{subscription}', [EmailSubscriptionController::class, 'destroy'])->name('destroy');
    });

    // Scholarship Routes
    Route::prefix('scholarships')->name('scholarships.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Backend\ScholarshipController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Backend\ScholarshipController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Backend\ScholarshipController::class, 'store'])->name('store');
        Route::get('/{scholarship}/edit', [\App\Http\Controllers\Backend\ScholarshipController::class, 'edit'])->name('edit');
        Route::put('/{scholarship}', [\App\Http\Controllers\Backend\ScholarshipController::class, 'update'])->name('update');
        Route::get('/{scholarship}', [\App\Http\Controllers\Backend\ScholarshipController::class, 'show'])->name('show');
        Route::delete('/{scholarship}', [\App\Http\Controllers\Backend\ScholarshipController::class, 'destroy'])->name('destroy');
        Route::delete('/delete/bulk-delete', [\App\Http\Controllers\Backend\ScholarshipController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Scholarship Applications Routes
    Route::prefix('scholarship-applications')->name('scholarship-applications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Backend\ScholarshipApplicationController::class, 'index'])->name('index');
        Route::get('/{scholarshipApplication}', [\App\Http\Controllers\Backend\ScholarshipApplicationController::class, 'show'])->name('show');
        Route::post('/{id}/update-status', [\App\Http\Controllers\Backend\ScholarshipApplicationController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{scholarshipApplication}', [\App\Http\Controllers\Backend\ScholarshipApplicationController::class, 'destroy'])->name('destroy');
        Route::delete('/delete/bulk-delete', [\App\Http\Controllers\Backend\ScholarshipApplicationController::class, 'bulkDelete'])->name('bulk-delete');
    });

    // Scholarship Evaluation Routes
    Route::prefix('scholarship-evaluation')->name('scholarship-evaluation.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Backend\ScholarshipEvaluationController::class, 'index'])->name('index');
        Route::get('/application/{application}/create', [\App\Http\Controllers\Backend\ScholarshipEvaluationController::class, 'create'])->name('create');
        Route::post('/application/{application}', [\App\Http\Controllers\Backend\ScholarshipEvaluationController::class, 'store'])->name('store');
        Route::get('/{scholarshipEvaluation}', [\App\Http\Controllers\Backend\ScholarshipEvaluationController::class, 'show'])->name('show');
        Route::get('/{scholarshipEvaluation}/edit', [\App\Http\Controllers\Backend\ScholarshipEvaluationController::class, 'edit'])->name('edit');
        Route::put('/{scholarshipEvaluation}', [\App\Http\Controllers\Backend\ScholarshipEvaluationController::class, 'update'])->name('update');
        Route::delete('/{scholarshipEvaluation}', [\App\Http\Controllers\Backend\ScholarshipEvaluationController::class, 'destroy'])->name('destroy');
        Route::delete('/delete/bulk-delete', [\App\Http\Controllers\Backend\ScholarshipEvaluationController::class, 'bulkDelete'])->name('bulk-delete');
    });

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

    Route::prefix('media-manager')->name('media-manager.')->group(function () {
        Route::get('/', [MediaManagerController::class, 'index'])->name('index');
        Route::post('/folders', [MediaManagerController::class, 'store'])->name('folders.store');
        Route::put('/folders/{folder}', [MediaManagerController::class, 'update'])->name('folders.update');
        Route::delete('/folders/{folder}', [MediaManagerController::class, 'destroy'])->name('folders.destroy');
        Route::post('/folders/{folder}/upload', [MediaManagerController::class, 'upload'])
            ->name('folders.upload')
            ->middleware('check.upload.limits');
        Route::put('/media/{media}', [MediaManagerController::class, 'updateMedia'])->name('media.update');
        Route::delete('/media/{media}', [MediaManagerController::class, 'destroyMedia'])->name('media.destroy');
    });

    // Media Routes.
    Route::prefix('document')->name('document.')->group(function () {
        Route::get('/', [DocumentRepositoryController::class, 'index'])->name('index');
        Route::get('/api', [DocumentRepositoryController::class, 'api'])->name('api');
        Route::post('/', [DocumentRepositoryController::class, 'store'])->name('store')->middleware('check.upload.limits');
        Route::get('/upload-limits', [DocumentRepositoryController::class, 'getUploadLimits'])->name('upload-limits');

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

        // Workflow routes
        Route::post('/{id}/change-status', [DocumentRepositoryController::class, 'changeStatus'])->name('document.change-status');
        Route::get('/{id}/workflow-history', [DocumentRepositoryController::class, 'workflowHistory'])->name('workflow-history');
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

        // Workflow routes
        Route::post('/{id}/change-status', [EducationRepositoryController::class, 'changeStatus'])->name('education.change-status');
        Route::get('/{id}/workflow-history', [EducationRepositoryController::class, 'workflowHistory'])->name('workflow-history');
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

        // Workflow routes
        Route::post('/{id}/change-status', [OthersController::class, 'changeStatus'])->name('others.change-status');
        Route::get('/{id}/workflow-history', [OthersController::class, 'workflowHistory'])->name('workflow-history');
    });

    // Editor Upload Route.
    Route::post('/editor/upload', [App\Http\Controllers\Backend\EditorController::class, 'upload'])->name('editor.upload');

    // AI Content Generation Routes.
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/providers', [App\Http\Controllers\Backend\AiContentController::class, 'getProviders'])->name('providers');
        Route::post('/generate-content', [App\Http\Controllers\Backend\AiContentController::class, 'generateContent'])->name('generate-content');
    });

    // remember to remove this below code after finalizing this project
    Route::get('/clear-cache', function () {
        if (app()->environment('local')) {
            Artisan::call('optimize:clear');
            return "✅ Cache cleared in local environment!";
        }
        abort(403, 'Unauthorized action.');
    });

    Route::get('/run-migrate', function () {
        Artisan::call('migrate', ['--force' => true]);
        return "Migration completed!";
    });

    Route::get('/run-seed', function () {
        // Auto-discover all seeders
        $seederPath = database_path('seeders');
        $seederFiles = File::glob($seederPath . '/*Seeder.php');

        $allSeeders = collect($seederFiles)
            ->map(fn ($file) => basename($file, '.php'))
            ->filter(fn ($seeder) => $seeder !== 'DatabaseSeeder') // Exclude main seeder
            ->values()
            ->toArray();

        $excludeSeeders = ['UserSeeder', 'ContentSeeder', 'TestUserProfileFieldsSeeder', 'SettingsSeeder']; // ← Customize exclusions

        $seedersToRun = array_diff($allSeeders, $excludeSeeders);

        $results = [];
        foreach ($seedersToRun as $seeder) {
            try {
                Artisan::call('db:seed', [
                    '--class' => $seeder,
                    '--force' => true,
                ]);
                $results[] = "✓ {$seeder}";
            } catch (\Exception $e) {
                $results[] = "✗ {$seeder}: " . $e->getMessage();
            }
        }

        return response()->json([
            'message' => 'Seeding completed!',
            'excluded' => $excludeSeeders,
            'results' => $results,
        ], 200);
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
Route::get('/demo-preview', fn () => view('demo.preview'))->name('demo.preview');

Route::get('/media/ahc-leaders/{path}', function (string $path) {
    if (str_contains($path, '..')) {
        abort(404);
    }

    $storagePath = 'ahc-leaders/' . ltrim($path, '/');

    if (! Storage::disk('public')->exists($storagePath)) {
        abort(404);
    }

    return response()->file(Storage::disk('public')->path($storagePath));
})->where('path', '.*')->name('media.ahc-leaders');
