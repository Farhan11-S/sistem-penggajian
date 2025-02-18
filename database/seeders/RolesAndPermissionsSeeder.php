<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissionsByRole = [
            'admin' => [
                'user',
                'karyawan'
            ],
            'karyawan' => [
                'presensi',
            ],
            'personalia' => [
                'gaji',
            ],
            'accountant' => [
                'review',
            ],
            'kasir' => [
                'pencairan',
            ]
        ];

        $insertPermissions = fn($role) => collect($permissionsByRole[$role])
            ->map(fn($name) => Permission::firstOrCreate(['name' => $name], ['guard_name' => 'web']));

        $permissionIdsByRole = [
            'admin' => $insertPermissions('admin'),
            'karyawan' => $insertPermissions('karyawan'),
            'personalia' => $insertPermissions('personalia'),
            'accountant' => $insertPermissions('accountant'),
            'kasir' => $insertPermissions('kasir'),
        ];

        $count = 1;
        foreach ($permissionIdsByRole as $role => $permissionIds) {
            Role::unguard();
            $role = Role::updateOrCreate(['id' => $count], ['name' => $role]);
            $count++;
            Role::reguard();

            $permissionIdsArray = $permissionIds
                ->filter(fn($permission) => !$role->hasPermissionTo($permission->name))
                ->map(fn($permission) => $permission->id)
                ->toArray();

            DB::table('role_has_permissions')
                ->insert(
                    collect($permissionIdsArray)->map(fn($id) => [
                        'role_id' => $role->id,
                        'permission_id' => $id
                    ])->toArray()
                );
        }
    }
}
