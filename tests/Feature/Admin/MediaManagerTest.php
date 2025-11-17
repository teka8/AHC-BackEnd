<?php

declare(strict_types=1);

use App\Http\Middleware\CheckPhpUploadLimits;
use App\Http\Middleware\VerifyCsrfToken;
use App\Models\MediaFolder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Storage;
use App\Models\Media;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

beforeEach(function (): void {
    $databasePath = database_path('testing.sqlite');

    if (! file_exists($databasePath)) {
        touch($databasePath);
    }

    putenv('DB_CONNECTION=sqlite');
    putenv('DB_DATABASE=' . $databasePath);
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_ENV['DB_DATABASE'] = $databasePath;
    $_SERVER['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_DATABASE'] = $databasePath;
    $_SERVER['REQUEST_METHOD'] = 'POST';

    config()->set('database.default', 'sqlite');
    config()->set('database.connections.sqlite.database', $databasePath);
    config()->set('filesystems.default', 'public');

    Schema::connection('sqlite')->disableForeignKeyConstraints();

    foreach ([
        'role_has_permissions',
        'model_has_permissions',
        'model_has_roles',
        'permissions',
        'roles',
        'media',
        'media_folders',
        'users',
    ] as $table) {
        Schema::connection('sqlite')->dropIfExists($table);
    }

    Schema::connection('sqlite')->enableForeignKeyConstraints();

    Schema::connection('sqlite')->create('users', function (Blueprint $table) {
        $table->id();
        $table->string('first_name');
        $table->string('last_name');
        $table->string('email')->unique();
        $table->string('username')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->rememberToken();
        $table->timestamps();
    });

    Schema::connection('sqlite')->create('roles', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('guard_name')->default('web');
        $table->timestamps();
    });

    Schema::connection('sqlite')->create('permissions', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('guard_name')->default('web');
        $table->timestamps();
    });

    Schema::connection('sqlite')->create('role_has_permissions', function (Blueprint $table) {
        $table->unsignedBigInteger('permission_id');
        $table->unsignedBigInteger('role_id');
        $table->primary(['permission_id', 'role_id']);
    });

    Schema::connection('sqlite')->create('model_has_roles', function (Blueprint $table) {
        $table->unsignedBigInteger('role_id');
        $table->string('model_type');
        $table->unsignedBigInteger('model_id');
        $table->index(['model_id', 'model_type']);
        $table->primary(['role_id', 'model_id', 'model_type']);
    });

    Schema::connection('sqlite')->create('model_has_permissions', function (Blueprint $table) {
        $table->unsignedBigInteger('permission_id');
        $table->string('model_type');
        $table->unsignedBigInteger('model_id');
        $table->index(['model_id', 'model_type']);
        $table->primary(['permission_id', 'model_id', 'model_type']);
    });

    Schema::connection('sqlite')->create('media_folders', function (Blueprint $table) {
        $table->id();
        $table->uuid('uuid')->unique();
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->foreignId('parent_id')->nullable();
        $table->foreignId('created_by')->nullable();
        $table->foreignId('updated_by')->nullable();
        $table->unsignedInteger('order_column')->default(0);
        $table->timestamps();
        $table->index('parent_id');
        $table->index('order_column');
    });

    Schema::connection('sqlite')->create('media', function (Blueprint $table) {
        $table->id();
        $table->string('model_type')->nullable();
        $table->unsignedBigInteger('model_id')->default(0);
        $table->uuid()->nullable()->unique();
        $table->string('collection_name');
        $table->string('name');
        $table->string('file_name');
        $table->string('mime_type')->nullable();
        $table->string('disk');
        $table->string('conversions_disk')->nullable();
        $table->unsignedBigInteger('size');
        $table->json('manipulations');
        $table->json('custom_properties');
        $table->json('generated_conversions');
        $table->json('responsive_images');
        $table->unsignedInteger('order_column')->nullable();
        $table->foreignId('folder_id')->nullable();
        $table->timestamps();
        $table->index('order_column');
        $table->index('folder_id');
    });

    $this->withoutMiddleware([
        VerifyCsrfToken::class,
        CheckPhpUploadLimits::class,
    ]);

    Storage::fake('public');

    $admin = User::factory()->create();
    $role = Role::create(['name' => 'media-admin']);

    Permission::create(['name' => 'media.view']);
    Permission::create(['name' => 'media.create']);
    Permission::create(['name' => 'media.update']);
    Permission::create(['name' => 'media.edit']);
    Permission::create(['name' => 'media.delete']);

    $role->syncPermissions([
        'media.view',
        'media.create',
        'media.update',
        'media.edit',
        'media.delete',
    ]);

    $admin->assignRole($role);

    test()->admin = $admin;
});

it('renders the media manager index', function (): void {
    $response = $this->actingAs(test()->admin)
        ->get(route('admin.media-manager.index'));

    $response->assertOk();
    $response->assertSee(__('Media Folders'));
});

it('allows creating folders and uploading media', function (): void {
    $this->actingAs(test()->admin)
        ->post(route('admin.media-manager.folders.store'), [
            '_source' => 'create-folder',
            'name' => 'Gallery',
        ])
        ->assertRedirect();

    $folder = MediaFolder::first();

    expect($folder)->not()->toBeNull();
    expect($folder->name)->toBe('Gallery');

    $this->post(route('admin.media-manager.folders.store'), [
        '_source' => 'create-folder',
        'name' => 'Gallery Child',
        'parent_id' => $folder->getKey(),
    ])->assertRedirect();

    $childFolder = MediaFolder::where('name', 'Gallery Child')->first();
    expect($childFolder)->not()->toBeNull();
    expect($childFolder->parent_id)->toBe($folder->getKey());

    $file = UploadedFile::fake()->create('hero.jpg', 120, 'image/jpeg');

    $this->post(route('admin.media-manager.folders.upload', $folder), [
        'files' => [$file],
        'captions' => ['0' => 'Gallery hero caption'],
    ])->assertRedirect(route('admin.media-manager.index', ['folder' => $folder->getKey()]));

    $media = Media::first();

    expect($media)->not()->toBeNull();
    expect($media->folder_id)->toBe($folder->getKey());
    expect($media->name)->toBe('hero');
    expect($media->collection_name)->toBe('folder_media');
    expect($media->getCustomProperty('caption'))->toBe('Gallery hero caption');

    Storage::disk('public')->assertExists('media/' . $media->file_name);
});

it('updates captions and deletes media from a folder', function (): void {
    $this->actingAs(test()->admin);

    $folder = MediaFolder::factory()->create();

    $file = UploadedFile::fake()->create('landscape.jpg', 512, 'image/jpeg');

    $this->post(route('admin.media-manager.folders.upload', $folder), [
        'files' => [$file],
    ])->assertRedirect();

    $media = Media::firstOrFail();

    $this->put(route('admin.media-manager.media.update', $media), [
        'caption' => 'Sunset over the hills',
    ])->assertRedirect(route('admin.media-manager.index', ['folder' => $folder->getKey()]));

    $media->refresh();
    expect($media->getCustomProperty('caption'))->toBe('Sunset over the hills');

    $this->delete(route('admin.media-manager.media.destroy', $media))
        ->assertRedirect(route('admin.media-manager.index', ['folder' => $folder->getKey()]));

    expect(Media::count())->toBe(0);
    Storage::disk('public')->assertMissing('media/' . $media->file_name);
});
