<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Schema;
// use Spatie\Permission\Contracts\Permission;
use App\Models\Permission;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();
        Schema::enableForeignKeyConstraints();

        collect([
            [
                'name' => 'All',
                'slug' => '*',
                'permissions' => Permission::all()->pluck('id')
            ],
            [
                'name' => 'System Admin',
                'slug' => 'system-admin',
                'permissions' => Permission::all()->pluck('id')

            ],
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'permissions' => Permission::all()->pluck('id')
            ],
            [
                'name' => 'Store Admin',
                'slug' => 'store-admin',
                'permissions' => [
                    'browse activity logs',
                    'bulk action activity logs',
                    'restore activity logs',
                    'view activity logs',
                    'browse accounts',
                    'browse users',
                    'bulk action users',
                    'add user',
                    'edit user',
                    'view user',
                    'delete user',
                    'browse orders',
                    'order bulk action',
                    'order store handle',
                    'order return stock',
                    'order downdoal recipe',
                    'browse reports',
                    'browse recipe',
                    'browse return stock',
                    'stock bulk action',
                    'view return stock',
                    'edit return stock',
                    'delete return stock',
                    'browse settings',
                    'browse currency',
                    'bulk action currency',
                    'add currency',
                    'edit currency',
                    'view currency',
                    'delete currency',
                    'browse drivers',
                    'browse driver drop-offs',
                    'scan out by filter',
                    'dropoff by filter',
                    'driver bulk actions',
                    'recipe scanner'
                ]
            ],
            [
                'name' => 'Store Owner',
                'slug' => 'store-owner',
                'permissions' => [
                    'browse orders',
                    'order bulk action',
                    'order return stock',
                    'order downdoal recipe',
                    'browse reports',
                    'browse recipe',
                    'recipe scanner',
                    'browse return stock',
                    'stock bulk action',
                    'view return stock',
                    'edit return stock',
                    'delete return stock',
                    'quick edit currency',
                    'browse drivers',
                    'browse driver drop-offs',
                    'scan out by filter',
                    'dropoff by filter',
                    'driver bulk actions',
                    'recipe scanner'
                ]
            ],
            [
                'name' => 'Cashier',
                'slug' => 'cashier',
                'permissions' => ['browse orders']
            ],
            [
                'name' => 'Driverman',
                'slug' => 'driverman',
                'permissions' => [
                    'browse reports',
                    'browse driver drop-offs',
                    'recipe scanner'
                ]
            ],
            [
                'name' => 'Customer',
                'permissions' =>  [
                    'browse orders'
                ],
            ],
        ])->map(function ($item) {
            $role = Role::query()
                ->create([
                    'name' => $item['name'],
                    'guard_name' => 'web'
                ]);
            $role->syncPermissions($item['permissions']);
        });
    }
}
