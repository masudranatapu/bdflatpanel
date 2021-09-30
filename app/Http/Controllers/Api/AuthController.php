<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\Api\Auth\AuthInterface;
use Illuminate\Http\Request;
use Validator;

class AuthController extends Controller
{
    protected $auth;

    function __construct(AuthInterface $auth) {
        $this->auth = $auth;
        date_default_timezone_set('Asia/Dhaka');
    }

    public function postLogin(Request $request)
    {
        $response = $this->auth->postLogin($request);

        return response()->json($response, $response->code);
    }

    /*public function postVerifyMobile(Request $request)
    {
        $response = $this->auth->postVerifyMobileNo($request);

        return response()->json($response, $response->code);
    }

    public function postVerifyCode(Request $request)
    {
        $response = $this->auth->postVerifyCode($request);

        return response()->json($response, $response->code);
    }

    public function postUpdateProfileData(Request $request)
    {
        $response = $this->auth->postUpdateProfileData($request);

        return response()->json($response, $response->code);
    }*/
}
