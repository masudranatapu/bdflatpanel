<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaymentBankRequest;
use App\Models\PaymentBank;
use App\Models\PaymentMethod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\PaymentBank\PaymentBankInterface;

class PaymentBankController extends BaseController
{
    protected $paymentbank;
    protected $resp;

    public function __construct(PaymentBankInterface  $paymentbank )
    {
        parent::__construct();
        $this->paymentbank         = $paymentbank;

    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->paymentbank->getPaginatedList($request, 50);
        return view('admin.paymentbank.index')->withRows($this->resp->data);
    }

    public function getCreate()
    {
        $data['methods'] = PaymentMethod::all()
            ->where('IS_ACTIVE', '=', 1)
            ->pluck('NAME', 'PK_NO');
        $data['status'] = [
            1 => 'Active',
            0 => 'Inactive'
        ];
        return view('admin.paymentbank.create', compact('data'));
    }

    public function postStore(PaymentBankRequest $request): RedirectResponse
    {
        $this->resp = $this->paymentbank->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit($id)
    {
        $data['methods'] = PaymentMethod::all()
            ->where('IS_ACTIVE', '=', 1)
            ->pluck('NAME', 'PK_NO');
        $data['status'] = [
            1 => 'Active',
            0 => 'Inactive'
        ];
        $data['account'] = PaymentBank::find($id);
        return view('admin.paymentbank.edit', compact('data'));
    }

    public function postUpdate(PaymentBankRequest $request, $id): RedirectResponse
    {
        $this->resp = $this->paymentbank->postUpdate($request, $id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    // public function putUpdate(AccountRequest $request, $PK_NO) {

    //     $this->resp = $this->account->postUpdate($request, $PK_NO);

    //     return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    // }

    // public function getDelete($PK_NO) {

    //     $this->resp = $this->account->delete($PK_NO);

    //     return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    // }

}
