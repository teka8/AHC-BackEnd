<?php

namespace App\Console\Commands;

use App\Services\PermissionService;
use Illuminate\Console\Command;

class CreatePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create missing permissions from PermissionService';

    /**
     * Execute the console command.
     */
    public function handle(PermissionService $permissionService)
    {
        $this->info('Creating permissions...');
        $permissions = $permissionService->createPermissions();
        $this->info('Created ' . count($permissions) . ' permissions successfully!');
    }
}
