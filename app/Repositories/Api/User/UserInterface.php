<?php

namespace App\Repositories\Api\User;

interface UserInterface
{
    public function getUserList();
    public function checkToken($request);
}
