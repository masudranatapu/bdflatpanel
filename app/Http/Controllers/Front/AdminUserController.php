<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiBaseController;
use Illuminate\Http\Request;
use App\Repositories\AdminUser\AdminUserRepoInterface;

class AdminUserController extends ApiBaseController
{
    protected $user;

    public function __construct(AdminUserRepoInterface $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        $users = $this->user->getPaginatedList();
        return $this->sendResponse($users->toArray(), 'Users retrieved successfully.');
    }
}
