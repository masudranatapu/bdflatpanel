<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\NotifySms\NotifySmsInterface;

class NotifyApiController extends BaseController
{

    protected $notifyInt;

    public function __construct(NotifySmsInterface $notifyInt )
    {
        $this->notifyInt  = $notifyInt;
    }

    public function getIndex(Request $request)
    {
        $this->resp = $this->notifyInt->getPaginatedList($request);
        return view('admin.notify.smslist')->withRows($this->resp->data);
    }


    public function getSendSms($id)
    {
        $this->resp = $this->notifyInt->getSendSms($id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getSendAllSms(Request $request)
    {
        $this->resp = $this->notifyInt->getSendAllSms($request);
        return 1;
    }

    public function getOrderDefault(Request $request)
    {
        $this->resp = $this->notifyInt->getOrderDefault($request);
        return 1;
    }
}
