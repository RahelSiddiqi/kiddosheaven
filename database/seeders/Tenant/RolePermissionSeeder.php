<?php

namespace Database\Seeders\Tenant;

use Illuminate\Database\Seeder;
use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissionGroups = [
            'Products' => ['view_products', 'create_products', 'edit_products', 'delete_products'],
            'Orders' => ['view_orders', 'create_orders', 'edit_orders', 'delete_orders', 'manage_order_status'],
            'Inventory' => ['view_inventory', 'manage_inventory', 'manage_batches', 'view_reports'],
            'Customers' => ['view_customers', 'edit_customers', 'delete_customers'],
            'Marketing' => ['view_marketing', 'manage_coupons', 'manage_flash_sales', 'manage_loyalty'],
            'Accounting' => ['view_accounting', 'manage_expenses', 'manage_partners', 'manage_investments'],
            'Settings' => ['view_settings', 'manage_settings', 'manage_users', 'manage_roles'],
        ];

        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $slug) {
                Permission::firstOrCreate(
                    ['slug' => $slug],
                    [
                        'name' => ucwords(str_replace('_', ' ', $slug)),
                        'group' => $group,
                    ]
                );
            }
        }

        // Create roles
        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Admin',
                'description' => 'Full system access',
                'is_default' => false,
            ]
        );

        $manager = Role::firstOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Manager',
                'description' => 'Manage products, orders, and inventory',
                'is_default' => false,
            ]
        );

        $customer = Role::firstOrCreate(
            ['slug' => 'customer'],
            [
                'name' => 'Customer',
                'description' => 'Customer access',
                'is_default' => true,
            ]
        );

        // Assign all permissions to admin (sync to avoid duplicates)
        $admin->permissions()->sync(Permission::all());

        // Assign limited permissions to manager
        $managerPermissions = Permission::whereIn('group', ['Products', 'Orders', 'Inventory', 'Customers', 'Marketing'])->get();
        $manager->permissions()->sync($managerPermissions);
    }
}
