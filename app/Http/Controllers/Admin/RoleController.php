<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Role\RoleInterface;
use App\Http\Requests\Admin\RoleRequest;
use Illuminate\Http\Request;
use DB;

class RoleController extends BaseController
{
    protected $role;

    public function __construct(RoleInterface $role)
    {
        $this->role = $role;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->role->getPaginatedList($request, 20);

        return view('admin.role.index')
            ->withRows($this->resp->data);
    }

    public function getCreate() {

        return view('admin.role.create')
            ->withGroups($this->role->getAllGroups());
    }

    public function postStore(RoleRequest $request)
    {
        $this->resp = $this->role->postStore($request, $request->only('permission'));

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request ,$id)
    {

        if (1 == $id) {
            return redirect()->route('admin.role')->with('flashMessageWarning', 'You can not edit super admin role !');
        }


        return view('admin.role.edit')
            ->withRole($this->role->findOrThrowException($id))
            ->withGroups($this->role->getAllGroups());
    }

    public function postUpdate(RoleRequest $request, $id)
    {
        if (1 == $id) {
            return redirect()->route('admin.role')->with('flashMessageWarning', 'You can not edit super admin role !');
        }

        $this->resp = $this->role->postUpdate($request->only('role_name'), $id, $request->only('permission'));

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        if (1 == $id) {
            return redirect()->route('admin.role')->with('flashMessageWarning', 'You can not delete super admin role !');
        }

        $this->resp = $this->role->delete($id);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
}
