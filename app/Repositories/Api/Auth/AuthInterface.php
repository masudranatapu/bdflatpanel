<?php

namespace App\Repositories\Api\Auth;


interface AuthInterface
{
    public function postLogin($inputs);
    
    /*public function postVerifyMobileNo($inputs);

    public function postVerifyCode($inputs);

    public function postUpdateProfileData($inputs);*/
}