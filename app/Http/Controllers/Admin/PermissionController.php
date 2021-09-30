<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Permission\PermissionInterface;
use App\Http\Requests\Admin\PermissionRequest;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use DB;

class PermissionController extends BaseController
{
    protected $permission;

    public function __construct(PermissionInterface $permission)
    {
        $this->permission = $permission;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->permission->getPaginatedList($request, 50);
        return view('admin.permission.index')->withRows($this->resp->data);
    }

    public function getCreate() {

        return view('admin.permission.create')
            ->withGroup($this->permission->getList());
    }

    public function postStore(PermissionRequest $request)
    {
        $this->resp = $this->permission->postStore($request);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id)
    {
        $this->resp = $this->permission->getShow($id);

        if (!$this->resp->status) {
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }

        return view('admin.permission.edit')
            ->withPermission($this->resp->data)
            ->withGroup($this->permission->getList());
    }

    public function putUpdate(PermissionRequest $request, $id)
    {
        $this->resp = $this->permission->postUpdate($request, $id);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->permission->delete($id);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
}
