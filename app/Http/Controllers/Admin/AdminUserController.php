<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\Admin\AdminUser\AdminUserInterface;
use App\Repositories\Admin\Role\RoleInterface;
use App\Repositories\Admin\UserGroup\UserGroupInterface;
use App\Http\Requests\Admin\AdminUserRequest;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use DB;

class AdminUserController extends BaseController
{
    protected $user;
    protected $role;
    protected $userGroup;

    public function __construct(AdminUserInterface $user, RoleInterface $role, UserGroupInterface $userGroup)
    {
        $this->user = $user;
        $this->role = $role;
        $this->userGroup = $userGroup;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->user->getPaginatedList($request);
        return view('admin.admin-user.index')
            ->withTriggers($this->resp->data);
    }

    public function getCreate(Request $request) {

        return view('admin.admin-user.create')->withUserGroup($this->userGroup->getList())->withRole($this->role->getList());
    }

    public function postStore(AdminUserRequest $request)
    {
        $this->resp = $this->user->postStore($request);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id)
    {
        $this->resp = $this->user->getShow($id);

        if (!$this->resp->status) {
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }

        return view('admin.admin-user.edit')->withUser($this->resp->data)->withUserGroup($this->userGroup->getList());

    }

    public function putUpdate(AdminUserRequest $request, $id, $type = null)
    {
        $this->resp = $this->user->postUpdate($request, $id,$type);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->user->delete($id);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEditSingle(Request $request, $id)
    {
        $this->resp = $this->user->getShow($id);

        if (!$this->resp->status) {
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }

        return view('admin.admin-user.singleEdit')->withUser($this->resp->data);
    }
}
