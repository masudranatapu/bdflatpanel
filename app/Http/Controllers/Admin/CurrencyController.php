<?php

namespace App\Http\Controllers\Admin;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Currency\CurrencyInterface;

class CurrencyController extends BaseController
{
    public function __construct(CurrencyInterface $curency, Currency $curency_model)
    {
        $this->curency         = $curency;
        $this->curency_model   = $curency_model;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->curency->getPaginatedList($request, 50);
        return view('admin.currency.index')->withRows($this->resp->data);
    }

    public function postStore(Request $request)
    {
        $this->resp = $this->curency->postStore($request);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function putUpdate(Request $request, $PK_NO) {

        $this->resp = $this->curency->postUpdate($request, $PK_NO);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($PK_NO) {

        $this->resp = $this->curency->delete($PK_NO);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }
}
