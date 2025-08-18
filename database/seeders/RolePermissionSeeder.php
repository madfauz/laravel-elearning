<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'create-class',
            'read-class',
            'update-class',
            'delete-class',

            'create-student',
            'read-student',
            'update-student',
            'delete-student',

            'create-teacher',
            'read-teacher',
            'update-teacher',
            'delete-teacher',
        ];

        $roles = [
            'admin',
            'teacher',
            'student',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
            ]);
        }

        foreach ($roles as $role) {
            $roleInstance = Role::firstOrCreate(['name' => $role]);

            if ($role === 'admin') {
                $roleInstance->givePermissionTo(Permission::all());
            } elseif ($role === 'teacher') {
                $roleInstance->givePermissionTo([
                    'create-class',
                    'read-class',
                    'update-class',
                    'delete-class'
                ]);
            } elseif ($role === 'student') {
                $roleInstance->givePermissionTo(['read-class']);
            }
        }
    }
}
