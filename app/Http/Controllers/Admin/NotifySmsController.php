<?php
namespace App\Http\Controllers\Admin;

use DB;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\EmailNotification;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\NotifySms\NotifySmsInterface;

class NotifySmsController extends BaseController
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

    public function getEmailIndex(Request $request)
    {
        $this->resp = $this->notifyInt->getEmailIndex($request);
        return view('admin.notify.emaillist')->withRows($this->resp->data);
    }

    public function getEmailBody($id)
    {
        $data = EmailNotification::find($id);
        $this->resp = $this->notifyInt->getEmailBody($id);

        if ($data->TYPE == 'Order Create') {
            $order_info = $this->notifyInt->getEmailBody($id);
            return view('admin.Mail.order_place')->withRows($order_info);
        }elseif($data->TYPE == 'Arrival'){
            $order_info = $this->notifyInt->getEmailBody($id);
            return view('admin.Mail.order_arrive')->withRows($order_info);
        }elseif($data->TYPE == 'Default'){
            $order_info = $this->notifyInt->getEmailBody($id);
            return view('admin.Mail.order_default')->withRows($order_info);
        }elseif($data->TYPE == 'Dispatch'){
            $order_info = $this->notifyInt->getEmailBody($id);
            return view('admin.Mail.order_dispatch')->withRows($order_info);
        }elseif($data->TYPE == 'Cancel'){
            $order_info = $this->notifyInt->getEmailBody($id);
            return view('admin.Mail.order_cancel')->withRows($order_info);
        }elseif($data->TYPE == 'Return'){
            $order_info = $this->notifyInt->getEmailBody($id);
            return view('admin.Mail.order_arrive')->withRows($order_info);
        }elseif($data->TYPE == 'greeting'){
            $order_info = $this->notifyInt->getEmailBody($id);
            return view('admin.Mail.greeting')->withRows($order_info);
        }
    }

    public function getSendSms($id)
    {
        $this->resp = $this->notifyInt->getSendSms($id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getSendEmail($id)
    {
        $this->resp = $this->notifyInt->getSendEmail($id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getSendAllSms(Request $request)
    {
        //$this->resp = $this->notifyInt->getSendAllSms($request);
        return 1;
    }







}
