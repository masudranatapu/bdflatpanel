<?php

use App\Models\City;
use App\Models\Brand;
use App\Models\State;
use App\Models\Hscode;
use App\Models\Category;
use App\Models\VatClass;
use App\Models\AdminUser;
use App\Models\SubCategory;
use App\Models\ProductModel;
use App\Models\Auth as CustomAuth;
use App\Models\Country;

if (!function_exists('getAuthId')) {

    function getAuthId()
    {
        if (Auth::user()) {
            $user_session = Auth::user();
            return $user_session->PK_NO;
        }
    }
}

if (!function_exists('userRolePermissionArray')) {
    function userRolePermissionArray() {
        $roles = DB::table('SA_ROLE_DTL')
            ->select('SA_ROLE_DTL.PERMISSIONS')
            ->join('SA_USER_GROUP_ROLE', 'SA_USER_GROUP_ROLE.F_ROLE_NO', 'SA_ROLE_DTL.F_ROLE_NO')
            ->join('SA_USER_GROUP_USERS','SA_USER_GROUP_USERS.F_GROUP_NO', 'SA_USER_GROUP_ROLE.F_USER_GROUP_NO')
            ->where('SA_USER_GROUP_USERS.F_USER_NO', getAuthId())
            ->first();

        if (! empty($roles)) {
            return explode(",", $roles->PERMISSIONS);
        }

        return [];
    }
}

if (!function_exists('hasRoleToThisUser')) {
    /**
     * Helper to return the current login user id
     *
     * @return mixed
     */
    function hasRoleToThisUser($user_id)
    {
        return DB::table('SA_USER_GROUP_USERS')
                ->join('SA_USER_GROUP_ROLE','SA_USER_GROUP_ROLE.F_USER_GROUP_NO','SA_USER_GROUP_USERS.F_GROUP_NO')
                ->where('SA_USER_GROUP_USERS.F_USER_NO', $user_id)
                ->value('SA_USER_GROUP_ROLE.F_ROLE_NO');
    }
}

if (!function_exists('hasAccessAbility')) {
    function hasAccessAbility($permission_slug, $permission_array) {
        $user_id = getAuthId();

        if ($user_id == 1) return true;

        $role_id = hasRoleToThisUser($user_id);

        if ($role_id == 1) return true;

        if (! empty($permission_slug) && ! empty($permission_array)) {
            if (in_array($permission_slug, $permission_array)) {
                return true;
            }
        }

        return false;
    }
}

/*
 *PHP Array into a PHP Object
 */
if (!function_exists('array_to_object')) {
    function array_to_object($array) {
        return (object) $array;
    }
}

/*
 *PHP Object into a PHP Array
 */
if (!function_exists('object_to_array')) {
    function object_to_array($object) {
        return (array) $object;
    }
}
/*Print+Exit = print */
if (!function_exists('prixt')) {

    function prixt($data, $exit = 0)
    {
        echo "<pre>";
        print_r($data);
        if($exit == 1)
        {
            exit;
        }
    }
}

/*Print Validation Error List*/
if (!function_exists('vError')) {

    function vError($errors)
    {
        if ($errors->any()){
            foreach ($errors->all() as $error){
                echo '<li class="text-danger">'. $error .'</li>';
            }
        }
        else {
            echo 'Not found any validation error';
        }

    }
}

if (!function_exists('get_error_response')) {

    function get_error_response($code, $reason, $errors = [],  $error_as_string = '', $description = '')
    {
        if ($error_as_string == '') {
            $error_as_string = $reason;
        }

        if ($description == '') {
            $description = $reason;
        }

        return [
            'code'          => $code,
            'errors'        => $errors,
            'error_as_string'  => $error_as_string,
            'reason'        => $reason,
            'description'   => $description,
            'error_code'    => $code,
            'link'          => ''
        ];
    }
}


if (!function_exists('getCategorCombo')) {
    function getCategorCombo() {
       return Category::where('IS_ACTIVE',1)->pluck('NAME', 'PK_NO');

    }
}

if (!function_exists('getSubCategorCombo')) {
    function getSubCategorCombo($category_id) {
       return SubCategory::where(['IS_ACTIVE' => 1, 'F_PRD_CATEGORY_NO' => $category_id])->pluck('NAME', 'PK_NO');

    }
}

if (!function_exists('getBrandCombo')) {
    function getBrandCombo() {
       return Brand::where('IS_ACTIVE',1)->pluck('NAME', 'PK_NO');

    }
}

if (!function_exists('getVatClassCombo')) {
    function getVatClassCombo() {
       return VatClass::where('IS_ACTIVE',1)->pluck('NAME', 'RATE');

    }
}

if (!function_exists('getModelCombo')) {
    function getModelCombo($brand_id) {
       return ProductModel::where(['IS_ACTIVE' => 1, 'F_PRD_BRAND_NO' => $brand_id])->pluck('NAME', 'PK_NO');

    }
}
if (!function_exists('getHScodeCombo')) {
    function getHScodeCombo($subcat_id) {
       return Hscode::where(['F_PRD_SUB_CATEGORY_NO' => $subcat_id])->pluck('CODE', 'PK_NO');

    }
}

if(!function_exists('getVariantName')){
    function getVariantName($booking_no) {
       return  DB::SELECT("select INV_STOCK.PRD_VARINAT_NAME,count(*) as ORD_QTY from
        SLS_BOOKING_DETAILS, INV_STOCK
        where SLS_BOOKING_DETAILS.F_BOOKING_NO = $booking_no
        and INV_STOCK.PK_NO = SLS_BOOKING_DETAILS.F_INV_STOCK_NO
        group by INV_STOCK.F_PRD_VARIANT_NO");
    }
}

if (!function_exists('getCityName')) {
    function getCityName($id) {
        $city_name = City::select('CITY_NAME')->where('PK_NO',$id)->first();
       return $city_name->CITY_NAME;

    }
}

if (!function_exists('getStateName')) {
    function getStateName($id) {
       $state_name = State::select('STATE_NAME')->where('PK_NO',$id)->first();
       return $state_name->STATE_NAME;
    }
}

if (!function_exists('getCountryName')) {
    function getCountryName($id) {
        $country_name = Country::select('NAME')->where('PK_NO',$id)->first();
       return $country_name->NAME;
    }
}
