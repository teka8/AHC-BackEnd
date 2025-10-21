<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EventPermissionsSeeder extends Seeder
{
    public function run()
    {

        $groupName = 'event';

        $perms = [
            'event.view',
            'event.create',
            'event.update',
            'event.delete',
            'event.bulk-delete',
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
