<?php
namespace App\Repositories\Api\Auth;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Auth;
use Validator;
use DB;

class AuthAbstract implements AuthInterface
{
    use ApiResponse;

    function __construct() {
    }

    public function postLogin($inputs)
    {
        if ($errors = $this->hasValidationErrorInLogin($inputs)) {
            return $this->errorResponse(200, 'Failed to pass validation !', $errors, 0);
        }

        $email = $inputs->email;
        $password = $inputs->password;
        $client = $inputs->header('user-agent');

        $auth_info = $this->getAuthInfo($email);

        if (!empty($auth_info)) {
            if (Hash::check($password, $auth_info->PASSWORD)) {
                $data = $this->userLoginResponse($auth_info, $client);
                return $this->successResponse(200, 'Successfully login !', $data, 1);
            } else {
                return $this->errorResponse(200, 'Doesn\'t match password !', null, 0);
            }
        }

        return $this->errorResponse(200, 'Doesn\'t match username !', null, 0);
    }

    private function hasValidationErrorInLogin($inputs) {

        $validator = Validator::make($inputs->all(), [
            'email'  => 'required',
            'password'  => 'required'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return false;
    }

    private function getAuthInfo($email)
    {
        $info = DB::table('SA_USER as a')
            ->select(
                'a.PK_NO',
                'a.USERNAME',
                'a.EMAIL',
                'a.MOBILE_NO',
                'a.PASSWORD',
                'a.USER_TYPE',
                'a.FIRST_NAME',
                'a.LAST_NAME',
                'r.F_ROLE_NO'
            )
            ->join('SA_USER_GROUP_USERS as u', 'u.F_USER_NO', 'a.PK_NO')
            ->join('SA_USER_GROUP_ROLE as r', 'r.F_USER_GROUP_NO', 'u.F_GROUP_NO')
            ->where( [ 'a.STATUS' => 1, 'a.CAN_LOGIN' => 1 ])
            ->where(function ($query) use ($email) {
                $query->where('a.EMAIL', $email);
            })
            ->first();
        return $info;
    }

    private function userLoginResponse($auth_obj, $client, $device_id = null)
    {
        $data = [];
        $token = $this->makeToken($auth_obj->PK_NO, $auth_obj->USER_TYPE, $client);

        $member_type = $auth_obj->USER_TYPE;

        $data['token']      = $token;
        $data['user_type']  = $member_type;
        $data['user_name']  = $auth_obj->USERNAME;
        $data['email']      = $auth_obj->EMAIL;
        $data['mobile_no']  = $auth_obj->MOBILE_NO;
        $data['auth_id']    = $auth_obj->PK_NO;
        $data['role_id']    = $auth_obj->F_ROLE_NO;

        $user_map = DB::table('SS_INV_USER_MAP')
        ->select('F_INV_WAREHOUSE_NO')
        ->where('F_USER_NO',$auth_obj->PK_NO)
        ->first();
        $data['user_location'] = $user_map->F_INV_WAREHOUSE_NO;
        /*$user_info = $this->{$auth_obj->model_key}->getUserDetails($auth_obj->id);

        if (!empty($user_info)) {
            $image_url = "";

            if ($user_info->avatar!= '') {
                $image_url = $user_info->avatar_url;
            }

            $data['first_name'] = $user_info->first_name;
            $data['middle_name'] = $user_info->middle_name;
            $data['last_name'] = $user_info->last_name;
           // $data['employee_code'] = $user_info->employee_code;
            $data['designation'] = $user_info->designation;
          //  $data['department'] = $user_info->department;
            $data['gender'] = $user_info->gender;
            $data['profile_picture_url'] = $image_url;
            $data['user_id'] = $user_info->id;
        }*/

        return $data;
    }

    private function makeToken($auth_id, $user_type, $client)
    {
        $token = hash('sha256', Str::random(30), false);

        //Set expire previous token
        DB::table('SA_TOKEN')
            ->where(['F_USER_NO' => $auth_id])
            ->update(['IS_EXPIRE' => 1]);

        DB::table('SA_TOKEN')->insert([
            'F_USER_NO'     => $auth_id,
            'TOKEN'         => $token,
            'CLIENT'        => $client,
            'STARTED_AT'    => date("Y-m-d H:i:s"),
            'EXPIRE_AT'     => date("Y-m-d H:i:s", strtotime("+365 day"))
        ]);

        return $token;
    }

    public function getUserList(){

    }

    /*public function postVerifyMobileNo($inputs)
    {
        if ($errors = $this->hasValidationErrorInVerifyMobile($inputs)) {
            return $this->errorResponse(200, 'Failed to pass validation !', $errors, 'FAILED');
        }

        $hasExist = $this->isExistMobileNo($inputs->mobile_no);

        if (!empty($hasExist)) {
            $user = $this->getUserData($hasExist->id);

            if ($user->password == '' && $user->is_first_login == 1) {

                $this->signIn       = false;
                $this->signUp       = false;
                $this->signUpData   = true;
                $this->isFirstLogin = true;

                $this->makeVerifyMobileNumberCode($user->mobile_no, $user->user_type);

                if ($user->activation_code == '') {
                    //Make new code
                    $auth_code = getGeneratedCode(20);

                    $auth = Auth::find($user->id);
                    $auth->activation_code  = $auth_code;

                    if ($auth->save()) {
                        $this->authCode = $auth_code;
                    }

                } else {
                    $this->authCode = $user->activation_code;
                }
            }

        } else {
            $this->signIn = false;
            $this->signUp = true;

            $this->makeVerifyMobileNumberCode($inputs['mobile_no'], $inputs['user_type']);
        }

        $this->data['sign_in'] = $this->signIn;
        $this->data['sign_up'] = $this->signUp;
        $this->data['sign_up_data'] = $this->signUpData;
        $this->data['is_first_login'] = $this->isFirstLogin;
        $this->data['auth_code'] = $this->authCode;

        return $this->successResponse(200, '', $this->data, 'OK');
    }*/

    /*private function hasValidationErrorInVerifyMobile($inputs) {

        $validator = Validator::make($inputs->all(), [
            'mobile_no' => 'required|min:11|max:11',
            //   'user_type' => 'required|numeric|between:1,3'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return false;
    }*/

    /*private function isExistMobileNo($mobile_no) {
        return DB::table('auths')
            ->select('id', 'mobile_no', 'password', 'activation_code', 'user_type')
            ->where('mobile_no', $mobile_no)
            ->first();
    }*/

    /*private function getUserData($auth_id) {
        return DB::table('auths as a')
            ->select('a.id', 'a.password', 'a.is_first_login', 'a.gender', 'u.first_name', 'u.middle_name', 'u.last_name')
            ->leftJoin('users as u','u.auth_id', '=', 'a.id')
            ->where('a.id', $auth_id)
            ->first();
    }*/

    /*protected function makeVerifyMobileNumberCode($mobile_no, $user_type)
    {
        DB::table('verify_mobile_no')
            ->where(['status'=> 1, 'mobile_no'=> $mobile_no, 'user_type'=>  $user_type, 'purpose'=>  0])
            ->update(['status' => 2]);

        $code = makeUniqueNumericCode(5);

        DB::table('verify_mobile_no')->insert(
            [
                'mobile_no'         => $mobile_no,
                'code'              => $code,
                'user_type'         => $user_type,
                'purpose'           => 0,
                'created_at'        => date('Y-m-d H:i:s'),
                'expire_at'         => "2030-01-06 10:12:03"
            ]
        );

        //Send sms
        if ($code != '') {
            $this->authCode = $code;
            sendSms($mobile_no, "Your verification code is ".$code);

            return true;
        }

        return $code;
    }*/

    /*public function postVerifyCode($inputs)
    {
        if ($errors = $this->validationVerifyCodeRequest($inputs)) {
            return $this->errorResponse(200, 'Failed to pass validation !', $errors, 'FAILED');
        }

        if ($this->verifyActivationCode($inputs->mobile_no, $inputs->user_type, $inputs->code)) {

            if (isset($inputs->auth_id) && $inputs->auth_id != "") {
                $auth_id = $inputs->auth_id;

                $info = DB::table('auths')
                    ->select('activation_code')
                    ->where('id', $auth_id)
                    //  ->where('user_type', $inputs['user_type'])
                    ->first();

                if (!empty($info)) {
                    $auth_code = $info->activation_code != '' ? $info->activation_code : getGeneratedCode(20);

                    DB::table('auths')
                    ->where(['id' => $auth_id])
                    ->update(['mobile_no' => $inputs->mobile_no, 'activation_code' => $auth_code]);

                } else {

                    return $this->errorResponse(200, 'Invalid auth id', null, 'FAILED');
                }

            }  else {
                $auth_code = getGeneratedCode(20);
                $this->insertToMemberTable(['mobile_no' => $inputs->mobile_no, 'user_type' => 1, 'model_id' => 2, 'activation_code' => $auth_code, 'created_at' => date('Y-m-d H:i:s')], 2);
            }

            return $this->successResponse(200, 'Successfully verify the code', null, 'OK');
        }

        return $this->errorResponse(200, 'Invalid code !', null, 'FAILED');
    }*/

    /*protected function validationVerifyCodeRequest($inputs) {

        $validator = Validator::make($inputs->all(), [
            'mobile_no' => 'required|min:11|max:11',
            'code'      => 'required|max:5',
            'user_type' => 'required|numeric|between:1,2'
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return false;
    }*/

    /*protected function verifyActivationCode($mobile_no, $user_type, $code, $purpose = 0)
    {
        $info = DB::table('verify_mobile_no')
            ->select('id', 'mobile_no')
            ->where(['status'=> 1, 'mobile_no'=> $mobile_no, 'user_type'=>  $user_type, 'code'=>  $code, 'purpose'=>  $purpose])
            ->first();

        if (empty($info)) {
            return false;
        }

        DB::table('verify_mobile_no')
            ->select('id', 'mobile_no')
            ->where(['status'=> 1, 'mobile_no'=> $mobile_no, 'user_type'=>  $user_type, 'code'=>  $code, 'purpose'=>  $purpose])
            ->update(['status' => 0]);

        return true;
    }*/

    /*private function insertToMemberTable(array $data_array, $role_id)
    {
        $data_array['password'] = '';

        $id = DB::table('auths')->insertGetId($data_array);

        if ($id > 0) {
            DB::table('auth_group_role')->insert([ 'role_id' => $role_id, 'auth_id' => $id, 'group_id' => 0 ]);
            return true;
        }

        return false;
    }*/

    /*public function postUpdateProfileData($inputs)
    {
        $client = $inputs->header('user-agent');

        if($errors = $this->validationUpdateProfileDataRequest($inputs)) {

            return $this->errorResponse(200, 'Failed to pass validation !', $errors, 'FAILED');
        }

        $auth_info = DB::table('auths as a')
            ->select('a.id', 'a.username','a.email', 'a.mobile_no', 'a.user_type', 'b.model_key')
            ->join('models as b', 'b.id', '=', 'a.model_id')
            ->where(['a.mobile_no' => $inputs['mobile_no'], 'a.user_type' => $inputs['user_type'], 'a.activation_code' => $inputs['authentication_code']])
            ->where('a.user_type', '!=', 0)
            ->first();

        if (!empty($auth_info)) {
            $user = Auth::find($auth_info->id);

            $user->password   =  Hash::make($inputs['password']);
            $user->status     = 1;
            $user->can_login  = 1;
            $user->activation_code = '';

            if ($user->save()) {

                $this->{$auth_info->model_key}->register($inputs, $auth_info->id);

                $device_id = '';

                if (isset($inputs['device_id'])) {
                    $device_id = $inputs['device_id'];
                }

                $data = $this->userLoginResponse($auth_info, $client, $device_id);

                return $this->successResponse(200, 'You are successfully registered !', $data, 'OK');
            }
        }

        return $this->errorResponse(200, 'Unable to save profile data !', $errors, 'FAILED');
    }*/

    /*protected function validationUpdateProfileDataRequest($inputs) {

        $validator = Validator::make($inputs->all(), [
            'mobile_no'     => 'required|exists:auths,mobile_no,user_type,' . $inputs['user_type'] . '|min:11|max:11',
            'user_type'     => 'required|numeric',
            'first_name'    => 'required|min:3|max:50',
            'last_name'     => 'required|min:3|max:50',
            'email'         => 'email',
            'password'      => 'required|min:6|max:60',
            'gender'        => 'required|numeric',
            'authentication_code'  => 'required',
        ]);

        if ($validator->fails()) {
            return $validator->errors();
        }

        return false;
    }*/
}

