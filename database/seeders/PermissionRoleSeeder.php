<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userRole = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'api']);
        $managerRole = Role::firstOrCreate(['name' => 'Manager', 'guard_name' => 'api']);

        $createTasks = Permission::firstOrCreate(['name' => 'Create Task', 'guard_name' => 'api']);
        $manageTaskData = Permission::firstOrCreate(['name' => 'Manage Task Data', 'guard_name' => 'api']);
        $updateTaskStatus = Permission::firstOrCreate(['name' => 'Update Task Status', 'guard_name' => 'api']);
        $updateTaskData = Permission::firstOrCreate(['name' => 'Update Task Data', 'guard_name' => 'api']);
        $viewTask = Permission::firstOrCreate(['name' => 'View Tasks', 'guard_name' => 'api']);

        $userRole->syncPermissions([$viewTask, $updateTaskStatus]);
        $managerRole->syncPermissions([$createTasks, $manageTaskData, $updateTaskData, $updateTaskStatus, $viewTask]);
    }
}
