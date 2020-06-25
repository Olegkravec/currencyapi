<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateBaseRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
//        \DB::statement("truncate table model_has_roles");
//        \DB::statement("truncate table model_has_permissions");
//        \DB::statement("truncate table role_has_permissions");
//        \DB::statement("truncate table permissions");
//        \DB::statement("truncate table roles");
//        \DB::statement("ALTER TABLE users AUTO_INCREMENT = 1");
        $permission_stack = [];
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin = Role::firstOrCreate(['name' => 'client']);

        {
            $permission = Permission::firstOrCreate(['name' => 'see all users']);
            array_push($permission_stack, $permission);
        }
        {
            $permission = Permission::firstOrCreate(['name' => 'edit users']);
            array_push($permission_stack, $permission);
        }

        $i_am_super_admin = \App\User::find(1);
        $i_am_super_admin->syncPermissions($permission_stack);
        $i_am_super_admin->assignRole('client', 'admin');
    }
}