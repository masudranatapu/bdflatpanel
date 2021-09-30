<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AccountSource;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\BankRequest;
use App\Http\Requests\Admin\AccountRequest;
use App\Repositories\Admin\Bank\BankInterface;

class BankAccountController extends BaseController
{
    public function __construct(BankInterface $bank_name, AccountSource $accountSource)
    {
        $this->bank_name       = $bank_name;
        $this->accountSource   = $accountSource;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->bank_name->getPaginatedList($request, 10);
        return view('admin.bank.index')->withRows($this->resp->data);
    }

    public function getCreateBank()
    {
        $data = array();
        $data['all_source'] = $this->accountSource->getAllSource();

        return view('admin.bank.create')->withData($data);
    }

    public function postStore(BankRequest $request) {

        $this->resp = $this->bank_name->postStore($request);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postStoreSingle(BankRequest $request) {

        $this->resp = $this->bank_name->postStoreSingle($request);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function putUpdate(BankRequest $request, $PK_NO) {

        $this->resp = $this->bank_name->postUpdate($request, $PK_NO);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($PK_NO) {

        $this->resp = $this->bank_name->delete($PK_NO);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }
}
