<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\AccountSource;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AccountRequest;
use App\Repositories\Admin\Account\AccountInterface;

class AccountController extends BaseController
{
    public function __construct(AccountInterface $account, AccountSource $accountSource)
    {
        $this->account         = $account;
        $this->accountSource   = $accountSource;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->account->getPaginatedList($request, 50);
        return view('admin.account.index')->withRows($this->resp->data);
    }

    public function getCreate()
    {
        return view('admin.account.create');
    }

    public function postAccSource(AccountRequest $request)
    {
        $this->resp = $this->account->postStore($request);
        //dd($this->resp);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function putUpdate(AccountRequest $request, $PK_NO) {

        $this->resp = $this->account->postUpdate($request, $PK_NO);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($PK_NO) {

        $this->resp = $this->account->delete($PK_NO);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }
}
