<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Team;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
  
    {

// صلاحيات عامة
$permissions = [
    'create team', 'update team', 'delete team',
    'create project', 'update project', 'delete project',
    'create task', 'update task', 'delete task',
    'add comment', 'update comment', 'delete comment',
    'add attachment', 'update attachment', 'delete attachment'
];

foreach ($permissions as $perm) {
    Permission::firstOrCreate(['name' => $perm,'guard_name'=>'sanctum']);  
}

// إنشاء أدوار مع صلاحيات (بدون teamId هنا، يتم تعيينها للفريق ديناميكيًا)
$roles = [
    'admin' => $permissions, // كاملة
    'project_manager' => [
        'create project', 'update project', 'delete project',
        'create task', 'update task', 'delete task',
        'add comment', 'update comment', 'delete comment',
        'add attachment', 'update attachment', 'delete attachment',
    ],
    'member' => [
        'create task', 'update task',
        'add comment', 'update comment', 'delete comment',
        'add attachment', 'update attachment', 'delete attachment'
    ]
];

foreach ($roles as $roleName => $perms) {
    $role = Role::firstOrCreate(['name' => $roleName,'guard_name'=>'sanctum']);
    $role->syncPermissions($perms);
}
    }
} 