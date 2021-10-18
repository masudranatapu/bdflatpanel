<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

trait CommonTrait {

    public function getVariantNo($request)
    {
        $product_variant_arr = [];
        $query  = $request->all();
        $result = array_intersect_key($query, array_flip(preg_grep("/^product_no_/", array_keys($query))));
        if(!empty($result)){
            $product_nos = implode(',', $result);
            $product_variant_arr = explode(',', $product_nos);
        }
        return $product_variant_arr;
    }
    public function getMyRole()
    {
        return DB::table('SA_USER_GROUP_USERS')->select('SA_USER_GROUP_ROLE.F_ROLE_NO as ROLE_NO','SA_ROLE.NAME as ROLE_NAME','SA_USER_GROUP.GROUP_NAME')->where('F_USER_NO', Auth::user()->PK_NO)->leftJoin('SA_USER_GROUP', 'SA_USER_GROUP.PK_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
        ->leftJoin('SA_USER_GROUP_ROLE', 'SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP.PK_NO')
        ->leftJoin('SA_ROLE', 'SA_ROLE.PK_NO','SA_USER_GROUP_ROLE.F_ROLE_NO')
        ->first();
    }




}
