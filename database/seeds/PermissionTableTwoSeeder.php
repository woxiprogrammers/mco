<?php

use Illuminate\Database\Seeder;
    use Illuminate\Support\Facades\DB;
    use Carbon\Carbon;

class PermissionTableTwoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        DB::table('permissions')->insert([
            [
                'name' => 'create-checklist-category',
                'module_id' => DB::table('modules')->where('slug','checklist-category')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-checklist-category',
                'module_id' => DB::table('modules')->where('slug','checklist-category')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-checklist-category',
                'module_id' => DB::table('modules')->where('slug','checklist-category')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-checklist-structure',
                'module_id' => DB::table('modules')->where('slug','checklist-structure')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-checklist-structure',
                'module_id' => DB::table('modules')->where('slug','checklist-structure')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-checklist-structure',
                'module_id' => DB::table('modules')->where('slug','checklist-structure')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-checklist-structure-site-assignment',
                'module_id' => DB::table('modules')->where('slug','checklist-structure-site-assignment')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-checklist-structure-site-assignment',
                'module_id' => DB::table('modules')->where('slug','checklist-structure-site-assignment')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-checklist-structure-site-assignment',
                'module_id' => DB::table('modules')->where('slug','checklist-structure-site-assignment')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-checklist-user-assignment',
                'module_id' => DB::table('modules')->where('slug','checklist-user-assignment')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-checklist-user-assignment',
                'module_id' => DB::table('modules')->where('slug','checklist-user-assignment')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-checklist-management',
                'module_id' => DB::table('modules')->where('slug','checklist-management')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-checklist-management',
                'module_id' => DB::table('modules')->where('slug','checklist-management')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'approve-checklist-management',
                'module_id' => DB::table('modules')->where('slug','checklist-management')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','approve')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-checklist-recheck',
                'module_id' => DB::table('modules')->where('slug','checklist-recheck')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-checklist-recheck',
                'module_id' => DB::table('modules')->where('slug','checklist-recheck')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-drawing-category',
                'module_id' => DB::table('modules')->where('slug','drawing-category')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-drawing-category',
                'module_id' => DB::table('modules')->where('slug','drawing-category')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-drawing-category',
                'module_id' => DB::table('modules')->where('slug','drawing-category')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-add-drawing',
                'module_id' => DB::table('modules')->where('slug','add-drawing')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-add-drawing',
                'module_id' => DB::table('modules')->where('slug','add-drawing')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-add-drawing',
                'module_id' => DB::table('modules')->where('slug','add-drawing')->pluck('id')->first(),
                'is_mobile' => false,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'create-manage-drawing',
                'module_id' => DB::table('modules')->where('slug','manage-drawing')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','create')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'view-manage-drawing',
                'module_id' => DB::table('modules')->where('slug','manage-drawing')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','view')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
            [
                'name' => 'edit-manage-drawing',
                'module_id' => DB::table('modules')->where('slug','manage-drawing')->pluck('id')->first(),
                'is_mobile' => true,
                'is_web' => true,
                'type_id' => DB::table('permission_types')->where('slug','edit')->pluck('id')->first(),
                'created_at' => $now,
                'updated_at' => $now
            ],
        ]);
    }
}
