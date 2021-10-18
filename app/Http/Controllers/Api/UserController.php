<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\Api\User\UserInterface;

class UserController extends Controller
{
    protected $user;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUserList(){

        $response = $this->user->getUserList();
        return response()->json($response, $response->code);
    }

    public function checkValidity(Request $request)
    {
        $response = $this->user->checkToken($request);
        return response()->json($response, $response->code);
    }
}
