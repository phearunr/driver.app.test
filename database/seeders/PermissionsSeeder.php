<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PermissionsSeeder extends Seeder
{
    /**s
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('roles')->truncate();
        Schema::enableForeignKeyConstraints();

        collect(
            [
                'activity_logs' => [
                    'browse activity logs',
                    'bulk action activity logs',
                    'restore activity logs',
                    'view activity logs',
                ],
                'roles' => [
                    'browse role',
                    'add role',
                    'edit role',
                    'view role',
                    'delete role',
                ],
                'permissioms' => [
                    'browse permissioms',
                    'add permissioms',
                    'edit permissioms',
                    'view permissioms',
                    'delete permissioms',
                ],
                'settings' => [
                    'browse settings',
                    'browse currency',
                    'bulk action currency',
                    'add currency',
                    'edit currency',
                    'view currency',
                    'delete currency',
                ],
                'accounts' => [
                    'browse accounts',
                    'browse users',
                    'bulk action users',
                    'add user',
                    'edit user',
                    'view user',
                    'delete user'
                ],
                'inventory' => [
                    'browse inventory',
                    'browse stores',
                    'browse handle',
                ],
                'reports' => [
                    'browse reports',
                    'browse recipe',
                    'browse return stock',
                    'stock store handle',
                    'stock bulk action',
                    'view return stock',
                    'edit return stock',
                    'delete return stock'
                ],
                'drivers' => [
                    'browse drivers',
                    'browse driver drop-offs',
                    'scan out by filter',
                    'dropoff by filter',
                    'driver bulk actions',
                    'recipe scanner',
                ],
                'contacts' => [
                    'browse contacts',
                    'suppliers',
                    'customers',
                    'customer groups',
                ],
                'products' => [
                    'browse products',
                    'list Products',
                    'variations',
                    'selling price group',
                    'categories',
                    'brands',
                ],
                'orders' => [
                    'browse orders',
                    'order bulk action',
                    'order store handle',
                    'quick edit currency',
                    'order return stock',
                    'order downdoal recipe'
                ]
            ]
        )->each(function ($model, $key) {
            foreach ($model as $item) {
                Permission::create([
                    'name' => $item,
                    'guard_name' => 'web'
                ]);
            }
        });
    }
}
