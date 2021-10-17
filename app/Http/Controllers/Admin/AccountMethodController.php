<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AccountSource;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\MethodRequest;
use App\Repositories\Admin\AccountMethod\AccountMethodInterface;

class AccountMethodController extends BaseController
{
    public function __construct(AccountMethodInterface $method_name, AccountSource $accountSource)
    {
        $this->method_name       = $method_name;
        $this->accountSource   = $accountSource;
    }

    // public function getIndex(Request $request)
    // {
    //     $this->resp = $this->method_name->getPaginatedList($request, 10);
    //     return view('admin.bank.index')->withRows($this->resp->data);
    // }

    // public function getCreateBank()
    // {
    //     $data = array();
    //     $data['all_source'] = $this->accountSource->getAllSource();

    //     return view('admin.bank.create')->withData($data);
    // }

    public function postStore(MethodRequest $request) {

        $this->resp = $this->method_name->postStore($request);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function putUpdate(MethodRequest $request, $PK_NO) {

        $this->resp = $this->method_name->postUpdate($request, $PK_NO);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($PK_NO) {

        $this->resp = $this->method_name->delete($PK_NO);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }
}
