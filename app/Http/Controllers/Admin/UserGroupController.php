<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\Admin\UserGroup\UserGroupInterface;
use App\Http\Requests\Admin\UserGroupRequest;
use App\Repositories\Admin\Role\RoleInterface;
use Illuminate\Http\Request;
use DB;

class UserGroupController extends BaseController
{
    protected $userGroup;
    protected $role;

    public function __construct(UserGroupInterface $userGroup, RoleInterface $role)
    {
        $this->userGroup = $userGroup;
        $this->role = $role;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->userGroup->getPaginatedList($request, 20);
        return view('admin.user-group.index')->withRows($this->resp->data);
    }

    public function getCreate() {
        return view('admin.user-group.create')->withRole($this->role->getList());;
    }

    public function postStore(UserGroupRequest $request)
    {
        $this->resp = $this->userGroup->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id)
    {
        $this->resp = $this->userGroup->getShow($id);
        if (!$this->resp->status) {
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }

        return view('admin.user-group.edit')
            ->withUserGroup($this->resp->data)->withRole($this->role->getList());
    }

    public function putUpdate(UserGroupRequest $request, $id)
    {
        $this->resp = $this->userGroup->postUpdate($request, $id);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->userGroup->delete($id);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
}
