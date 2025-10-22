<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class NewsPermissionsSeeder extends Seeder
{
    public function run()
    {

        $groupName = 'news';

        $perms = [
            'news.view',
            'news.create',
            'news.update',
            'news.delete',
            'news.bulk-delete',
            'news.approve',
            'news.publish'
        ];

        foreach ($perms as $p) {
            Permission::firstOrCreate(
                ['name' => $p],
                ['group_name' => $groupName]
            );
        }

        // assign to admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo($perms);

        // optionally assign directly to the first user (dev)
        $user = \App\Models\User::find(1);
        if ($user) {
            $user->givePermissionTo($perms);
        }
    }
}