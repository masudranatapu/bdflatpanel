<?php
namespace App\Http\Controllers\Admin;
use Response;
use Illuminate\Http\Request;
use App\Models\OrderConsignment;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\Poslazu\PoslazuInterface;

class PosLazuController extends BaseController
{
    protected $poslazuInt;

    public function __construct(PoslazuInterface $poslazuInt)
    {
        $this->poslazuInt     = $poslazuInt;
    }


    public function getConsignmentNote(Request $request,$id)
    {
        $check = OrderConsignment::where('COURIER_TRACKING_NO',$request->consignment_note)->first();
        if(!empty($check)){
            return redirect()->back()->with('flashMessageError', 'Consignment note duplicate not allow!');
        }else {
            $this->resp = $this->poslazuInt->getConsignmentNote($request);
            return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
        }
    }

    public function getTrackingId($id)
    {
        $data = $this->resp = $this->poslazuInt->getTrackingId($id);
        return Response::json($data);;

    }

public function postDispatch(Request $request,$id)
    {
        $this->resp = $this->poslazuInt->postStore($request);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);

    }










    public function createShipment(Request $request){

        $data = $this->poslazuInt->createShipment($request);

        return redirect()->route('admin.order.create-shipment');
    }

    public function get_cart_items(){

    }


     public function getShipmentByKey(){


     }

    public function checkout($key){

        $data = $this->poslazuInt->cartCheckout($key);

        dd($data);

        return redirect()->route('admin.shipment.shipment-status')->withData($data);

    }

    public function getShipmentStatus(){


        $data = $this->poslazuInt->getShipmentStatus();


        echo '<pre>';
        echo '======================<br>';
        print_r($data);
        echo '<br>======================';
        exit();


        return $data;
    }




    public function get_shipments(){


        $data = $this->poslazuInt->getShipmentStatus($request);


    }

    public function getCart(Request $request){

        $cartlist = $this->poslazuInt->getCartList($request);

        $data = $cartlist['data'];
        // echo '<pre>';
        // echo '======================<br>';
        // print_r($cartlist['data']);
        // echo '<br>======================';
        // exit();
        return view('admin.dispatch.shipment.cart')->withData($data);
    }


    public function downloadPdf(){


        return response()->download('http://sendparcel-test.ap-southeast-1.elasticbeanstalk.com/secure/print_thermal/DEMO31357105');
    }






}
