<?php

namespace App\Services;

/**
 * Created by
 * user: REZOAN LIKHON
 * Date: 12-Feb-2020
 * Time: 12:31 PM
 */
use DB;

class Access {

    public function can(string $permission_slug) {
        $auth_id = getAuthId();

        //If user is admin
        if ($auth_id == 1) return true;

        //Get user role id
        $role_id = $this->hasRole($auth_id);

        //If user role is admin
        if ($role_id == 1) return true;
        $roles = $this->has_permission($role_id,$permission_slug);

        if(count($roles) > 0) {
            return true;
        }

        return false;
    }

    private function hasRole(int $auth_id) {
        return DB::table('SA_USER')
                ->join('SA_USER_GROUP_USERS','SA_USER_GROUP_USERS.F_USER_NO','SA_USER.PK_NO')
                ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                ->where('SA_USER.PK_NO', $auth_id)
                ->value('SA_USER_GROUP_ROLE.F_ROLE_NO');
    }

    private function has_permission(int $role_id, string $permission_slug) {
        $permissions = DB::table('SA_ROLE_DTL')
            ->where('F_ROLE_NO', $role_id)
            ->where('PERMISSIONS','like', "%".$permission_slug."%")
            ->get();

        return $permissions;
    }
}
