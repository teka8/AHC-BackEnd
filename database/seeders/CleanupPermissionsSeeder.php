<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class CleanupPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Start a database transaction
        DB::beginTransaction();

        try {
            // Get all permissions with null group_name
            $permissions = Permission::whereNull('group_name')->get();
            
            if ($permissions->isNotEmpty()) {
                $this->command->info("Found {$permissions->count()} permissions with null group_name. Updating to 'uncategorized'...");
                
                // Update all null group_name to 'uncategorized'
                $updated = Permission::whereNull('group_name')
                    ->update(['group_name' => 'uncategorized']);
                    
                $this->command->info("Successfully updated $updated permissions.");
                
                // List the updated permissions
                $this->command->table(
                    ['ID', 'Name', 'Group Name'],
                    $permissions->map(function($permission) {
                        return [
                            'id' => $permission->id,
                            'name' => $permission->name,
                            'group_name' => $permission->group_name ?? 'null',
                        ];
                    })->toArray()
                );
            } else {
                $this->command->info('No permissions with null group_name found.');
            }
            
            // Commit the transaction
            DB::commit();
            
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollback();
            $this->command->error('Error cleaning up permissions: ' . $e->getMessage());
        }
    }
}
