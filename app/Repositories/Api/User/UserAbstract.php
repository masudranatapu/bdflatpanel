<?php
namespace App\Repositories\Api\User;
use Carbon\Carbon;
use App\Models\Token;
use App\Models\AdminUser;
use App\Traits\ApiResponse;

class UserAbstract implements UserInterface
{
    use ApiResponse;

    function __construct() {
    }

    public function getUserList(){

        $data = AdminUser::get();

        if (!empty($data)) {
            return $this->successResponse(200, 'Data is available !', $data, 1);
        }
        return $this->successResponse(200, 'Not found data !', null, 0);
    }

    public function checkToken($request){

        $current_time = Carbon::now();
        $data = Token::where('F_USER_NO', $request->user_id)->where('TOKEN',$request->Authorization)->first();

        if (!empty($data)) {
            if ($data->IS_EXPIRE == 1 || (date($current_time) > date($data->EXPIRE_AT))) {

                return $this->successResponse(200, 'Login Session Expired !', null, 0);
            }
            return $this->successResponse(200, 'Data is available !', null, 1);
        }
        return $this->successResponse(200, 'Invalid Token', $data, 0);
    }
}
