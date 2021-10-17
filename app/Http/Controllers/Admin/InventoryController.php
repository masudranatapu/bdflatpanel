<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
//use App\Repositories\Admin\UserGroup\UserGroupInterface;
//use App\Http\Requests\Admin\UserGroupRequest;
use Illuminate\Http\Request;
use DB;

class InventoryController extends BaseController
{
    protected $userGroup;

    public function __construct()
    {
        //$this->userGroup = $userGroup;
    }

    public function getIndex(Request $request)
    {       
        return view('admin.inventory.index');
    }

    public function getCreate() {

        return view('admin.inventory.create');
    }


   

    
}
