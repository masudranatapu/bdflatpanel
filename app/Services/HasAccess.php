<?php

namespace App\Services;

/**
 * Created by
 * user: REZOAN LIKHON
 * Date: 12-Feb-2020
 * Time: 12:31 PM
 */

use DB;

// use App\Models\Role;
// use App\Models\Permission;
// use App\Models\PermissionGroup;

class HasAccess {

    public function hasTokenValidity(string $token, $client) {

        $token_info = DB::table('SA_TOKEN as t')
            ->join('SA_USER as a', 'a.PK_NO', '=', 't.F_USER_NO')
            ->select('a.PK_NO')
            ->where(['a.STATUS' => 1, 'a.CAN_LOGIN' => 1])
            ->where(['t.TOKEN' => $token, 't.CLIENT' => $client])
            ->where('t.EXPIRE_AT', '>', date("Y-m-d H:i:s"))
            ->where('t.IS_EXPIRE', 0)
            ->orderBy('t.PK_NO', 'desc')
            ->first();

        if (!empty($token_info)) {
            return $token_info->PK_NO;
        }
        return null;
    }

    public function can($permission_slug, $auth_id){
        //Get auth role id
        $role_id = $this->hasRole($auth_id);

        if($role_id == 1){
            return true;
        }

        $roles = $this->has_permission($role_id, $permission_slug);
        if(count($roles) > 0) {
            return true;
        }
        return false;
    }

    private function hasRole($auth_id){
        return DB::table('SA_USER_GROUP_ROLE')
                    ->join('SA_USER_GROUP_USERS','SA_USER_GROUP_USERS.F_GROUP_NO','SA_USER_GROUP_ROLE.F_USER_GROUP_NO')
                    ->where('F_USER_NO', $auth_id)->value('F_ROLE_NO');
    }

    private function has_permission($role_id, $permission_slug){
        $permissions = DB::table('SA_ROLE_DTL')
            ->where('F_ROLE_NO', $role_id)
            ->where('PERMISSIONS','like', "%".$permission_slug."%")
            ->get();
        return $permissions;
    }
}
