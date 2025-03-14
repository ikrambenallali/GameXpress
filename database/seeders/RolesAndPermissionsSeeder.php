<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {


        $permissions = [
            'view_dashboard',
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $superAdmin = Role::create(['name' => 'super_admin']);
        $productManager = Role::create(['name' => 'product_manager']);
        $userManager = Role::create(['name' => 'user_manager']);

        $superAdmin->givePermissionTo($permissions);
        $productManager->givePermissionTo([
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'view_categories',
            'create_categories',
            'edit_categories',
            'delete_categories',
        ]);
        $userManager->givePermissionTo([
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
        ]);
        // $user = User::find(1);
        // $user->assignRole('super_admin');
    }
}
