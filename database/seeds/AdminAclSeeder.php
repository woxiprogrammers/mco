<?php

use Illuminate\Database\Seeder;

class AdminAclSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = \App\User::join('user_has_roles','user_has_roles.user_id','=','users.id')
            ->join('roles','roles.id','=','user_has_roles.role_id')
            ->where('roles.slug','admin')
            ->pluck('users.id')
            ->first();
        $permissionIds = \App\Permission::join('modules','modules.id','=','permissions.module_id')
                    ->where('modules.slug','!=','product-management-after-quotation-approve')
                    ->pluck('permissions.id')->toArray();
        foreach($permissionIds as $permissionId){
            \App\UserHasPermission::create([
                'user_id' => $userId,
                'permission_id' => $permissionId
            ]);
        }
    }
}
