<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\Admin\AdminUser\AdminUserInterface;
use App\Http\Requests\Admin\AssignAccessRequest;
use Illuminate\Http\Request;
use DB;

class AssignAccessController extends BaseController
{
    //protected $role;
    protected $user;

    public function __construct(AdminUserInterface $user)
    {
        $this->user = $user;
    }

    public function getIndex(Request $request)
    {
        return view('admin.assign-access.index')->withTriggers(0)->with('userName','');
    }
    
    public function postIndex(AssignAccessRequest $request)
    {
        $this->resp = $this->user->getSearchList($request);
        
        return view('admin.assign-access.index')->with('userName', $request->search_string)->withTriggers($this->resp->data);
    }

    
}
