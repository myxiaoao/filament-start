<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Misc
        $miscPermission = Permission::create(['name' => 'N/A', 'desc' => '基础权限']);

        // USER MODEL
        $userPermission1 = Permission::create(['name' => 'create-user', 'desc' => '创建用户']);
        $userPermission2 = Permission::create(['name' => 'read-user', 'desc' => '查看用户']);
        $userPermission3 = Permission::create(['name' => 'update-user', 'desc' => '更新用户']);
        $userPermission4 = Permission::create(['name' => 'delete-user', 'desc' => '删除用户']);

        // ROLE MODEL
        $rolePermission1 = Permission::create(['name' => 'create-role', 'desc' => '创建角色']);
        $rolePermission2 = Permission::create(['name' => 'read-role', 'desc' => '查看角色']);
        $rolePermission3 = Permission::create(['name' => 'update-role', 'desc' => '更新角色']);
        $rolePermission4 = Permission::create(['name' => 'delete-role', 'desc' => '删除角色']);

        // PERMISSION MODEL
        $permission1 = Permission::create(['name' => 'create-permission', 'desc' => '创建权限']);
        $permission2 = Permission::create(['name' => 'read-permission', 'desc' => '查看权限']);
        $permission3 = Permission::create(['name' => 'update-permission', 'desc' => '更新权限']);
        $permission4 = Permission::create(['name' => 'delete-permission', 'desc' => '删除权限']);

        // ADMINS
        $adminPermission1 = Permission::create(['name' => 'read-admin', 'desc' => '查看管理']);
        $adminPermission2 = Permission::create(['name' => 'update-admin', 'desc' => '更新管理']);

        // CREATE ROLES
        $userRole = Role::create(['name' => 'user', 'desc' => '普通用户'])->syncPermissions([
            $miscPermission,
        ]);

        $superAdminRole = Role::create(['name' => 'super-admin', 'desc' => '超级管理员'])->syncPermissions([
            $userPermission1,
            $userPermission2,
            $userPermission3,
            $userPermission4,
            $rolePermission1,
            $rolePermission2,
            $rolePermission3,
            $rolePermission4,
            $permission1,
            $permission2,
            $permission3,
            $permission4,
            $adminPermission1,
            $adminPermission2,
            $userPermission1,
        ]);
        $adminRole = Role::create(['name' => 'admin', 'desc' => '普通管理员'])->syncPermissions([
            $userPermission1,
            $userPermission2,
            $userPermission3,
            $userPermission4,
            $rolePermission1,
            $rolePermission2,
            $rolePermission3,
            $rolePermission4,
            $permission1,
            $permission2,
            $permission3,
            $permission4,
            $adminPermission1,
            $adminPermission2,
            $userPermission1,
        ]);

        $developerRole = Role::create(['name' => 'developer', 'desc' => '开发者'])->syncPermissions([
            $adminPermission1,
        ]);

        $faker = \Faker\Factory::create('zh_CN');

        // CREATE ADMINS & USERS
        User::create([
            'name'              => '超级管理员',
            'is_admin'          => 1,
            'email'             => 'super@admin.com',
            'email_verified_at' => now(),
            'phone'             => '18800000001',
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
        ])->assignRole($superAdminRole);

        User::create([
            'name'              => '普通管理员',
            'is_admin'          => 1,
            'email'             => 'admin@admin.com',
            'email_verified_at' => now(),
            'phone'             => $faker->phoneNumber,
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
        ])->assignRole($adminRole);


        User::create([
            'name'              => '开发者',
            'is_admin'          => 1,
            'email'             => 'developer@admin.com',
            'email_verified_at' => now(),
            'phone'             => $faker->phoneNumber,
            'password'          => Hash::make('password'),
            'remember_token'    => Str::random(10),
        ])->assignRole($developerRole);

        for ($i = 1; $i < 50; $i++) {
            User::create([
                'name'              => '测试用户-' . $faker->name,
                'is_admin'          => 0,
                'email'             => $faker->safeEmail,
                'email_verified_at' => now(),
                'phone'             => $faker->phoneNumber,
                'password'          => Hash::make('password'), // password
                'remember_token'    => Str::random(10),
                'created_at'        => now()->subMonths(random_int(0, 10))
            ])->assignRole($userRole);
        }
    }
}
