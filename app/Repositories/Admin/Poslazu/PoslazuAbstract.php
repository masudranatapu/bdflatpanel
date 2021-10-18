<?php
namespace App\Repositories\Admin\Poslazu;

use DB;
use Auth;
use App\Models\Agent;
use App\Models\Order;
use App\Models\Booking;
use App\Models\Country;
use App\Models\Dispatch;
use App\Traits\RepoResponse;
use App\Models\OrderConsignment;
use App\Models\CustomerAddressType;

class PoslazuAbstract implements PoslazuInterface
{
    use RepoResponse;
    protected $agent;
    protected $order;
    protected $booking;
    protected $address_type;
    protected $country;
    protected $dispatch;
    public function __construct(Agent $agent,Booking $booking, Order $order,CustomerAddressType $address_type, Country $country, Dispatch $dispatch)
    {
        $this->agent   = $agent;
        $this->booking = $booking;
        $this->order   = $order;
        $this->address_type = $address_type;
        $this->country = $country;
        $this->dispatch = $dispatch;
    }


    public function pregReplace($txt=NULL){
        return preg_replace('/[^A-Za-z0-9]/', ' ', $txt);
  }


  public function getConsignmentNote($request){
    $booking    = $this->booking->find($request->booking_no);
    $booking    = Booking::find($request->booking_no);
    $order      =  $booking->getOrder;

    //For Poslaju
    $api_key                    = env('POSLAJU_API_KEY');
    $send_method                = 'pickup';
    $send_date                  = date('Y-m-d', strtotime($request->dispatch_date));
    $type                       = 'parcel';
    $declared_weight            = $request->declared_weight;
    $provider_code              = 'poslaju';
    $size                       = $request->size;
    $length                     = $request->length;
    $width                      = $request->width;
    $height                     = $request->height;
    $content_type               = $request->content_type;
    $content_description        = 'Lifestyle,Home-Health and Fitness';
    //Required, number only without currency code, ie 200 for RM 200.00
    $content_value              = '200';
    $sender_name                = $order->FROM_NAME;
    $sender_phone               = $order->FROM_MOBILE;
    $sender_email               = 'azuramart@gmail.com';
    $sender_company_name        = $order->FROM_NAME;
    $sender_postcode            = '13700';
    $receiver_email             = 'azuramart@gmail.com';

    //Required
    if(!empty($order->FROM_ADDRESS_LINE_1)){
        $sender_address_line_1      = str_replace("&", ",", $order->FROM_ADDRESS_LINE_1);
    }
    else{
        return $this->formatResponse(false, 'FROM_ADDRESS_LINE_1 FIELD IS EMPTY !','admin.order.dispatch',$request->booking_no);
    }
    //Required
    if($order->FROM_POSTCODE){
        $sender_postcode         = $order->FROM_POSTCODE;
    }

    if(!empty($order->DELIVERY_NAME)){
        $receiver_name              = $order->DELIVERY_NAME;
    }
    else{
        return $this->formatResponse(false, 'DELIVERY_NAME FIELD IS EMPTY !','admin.order.dispatch',$request->booking_no);
    }

    //Required
    if(!empty($order->DELIVERY_MOBILE)){
        $receiver_phone             = $order->DELIVERY_MOBILE;
    }
    else{
        return $this->formatResponse(false, 'DELIVERY_MOBILE FIELD IS EMPTY !','admin.order.dispatch',$request->booking_no);
    }
    //Required
    if($order->DELIVERY_EMAIL){
        $receiver_email             = $order->DELIVERY_EMAIL;
    }

    $receiver_company_name      = NULL;
    //Required DELIVERY_ADDRESS_LINE_1
    if(!empty($order->DELIVERY_ADDRESS_LINE_1)){
        $receiver_address_line_1      = str_replace("&", ",", $order->DELIVERY_ADDRESS_LINE_1);
    }
    else{
        return $this->formatResponse(false, 'DELIVERY_ADDRESS_LINE_1 FIELD IS EMPTY !','admin.order.dispatch');
    }
    $receiver_address_line_2    = str_replace("&", ",", $order->DELIVERY_ADDRESS_LINE_2);
    $receiver_address_line_3    = str_replace("&", ",", $order->DELIVERY_ADDRESS_LINE_3);
    $receiver_address_line_4    = str_replace("&", ",", $order->DELIVERY_ADDRESS_LINE_4);
    //Required for domestic
    if(!empty($order->DELIVERY_POSTCODE)){
        $receiver_postcode          = $order->DELIVERY_POSTCODE;
    }
    else{
        return $this->formatResponse(false, 'DELIVERY_POSTCODE FIELD IS EMPTY !','admin.order.dispatch');
    }

    //Requried
    $receiver_country_code      = 'MY';
    //Post Data
    $data = "api_key=$api_key&send_method=$send_method&send_date=$send_date&type=$type&declared_weight=$declared_weight&provider_code=$provider_code&size=$size&length=$length&width=$width&height=$height&content_type=$content_type&content_description=$content_description&content_value=$content_value&sender_name=$sender_name&sender_phone=$sender_phone&sender_address_line_1=$sender_address_line_1&sender_postcode=$sender_postcode&receiver_name=$receiver_name&receiver_phone=$receiver_phone&receiver_email=$receiver_email&receiver_address_line_1=$receiver_address_line_1&receiver_address_line_2=$receiver_address_line_2&receiver_address_line_3=$receiver_address_line_3&receiver_address_line_4=$receiver_address_line_4&receiver_postcode=$receiver_postcode&receiver_country_code=$receiver_country_code";
    $post_url = env('POSLAJU_SHIPMENT');

      DB::beginTransaction();

      try {

          if($request->courier == 9)
          {
            $shipment = $this->apiPostRequest($post_url,$data);
            $shipmentData = json_decode($shipment, TRUE);
            $shipment_keys = $shipmentData['data']['key'];
            //CHECK SHIPMENT
            if(isset($shipment_keys)){

                //SHIPMENT CHECKOUT
                $checkout                          = $this->newCartCheckout($shipment_keys);
                $shipment                          = $this->getShipmentInfoBykey($shipment_keys);
                $data                              = json_decode($shipment, TRUE);
                $tracking_no                       = $data['data'][$shipment_keys]['tracking_no'];
                $shipment_cost                     = $data['data'][$shipment_keys]['effective_price'];
                $consignment                       = new OrderConsignment();
                $consignment->SHIPMENT_KEY         = $shipment_keys;
                $consignment->F_ORDER_NO           = $order->PK_NO;
                $consignment->COURIER_TRACKING_NO  = $tracking_no;
                $consignment->POSTAGE_COST         = $shipment_cost;
                $consignment->F_COURIER_NO         = $request->courier ?? null;
                $consignment->SS_CREATED_ON        = date('Y-m-d');
                $consignment->F_SS_CREATED_BY      = Auth::user()->PK_NO;
                $consignment->save();

            } else{
                return $this->formatResponse(false, 'SHIPMENT NOT CREATED !','admin.order.dispatch');
            }

        }else{

            $consignment = new OrderConsignment();
            $consignment->SHIPMENT_KEY              = NULL ;
            $consignment->F_ORDER_NO                = $order->PK_NO;
            $consignment->COURIER_TRACKING_NO       = $request->consignment_note;
            $consignment->POSTAGE_COST              = NULL;
            $consignment->F_COURIER_NO              = $request->courier ?? null;
            $consignment->SS_CREATED_ON             = date('Y-m-d');
            $consignment->F_SS_CREATED_BY           = Auth::user()->PK_NO;
            $consignment->save();

        }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false,'Consignment Note Created Unsuccessfull ','admin.order.list');
        }
        DB::commit();
        return $this->formatResponse(true,'Consignment Note Created Successfully !','admin.order.list');

  }

  public function  getTrackingId($id)
  {
      DB::beginTransaction();
          try {
              $consignment                 = OrderConsignment::select('SHIPMENT_KEY','COURIER_TRACKING_NO','POSTAGE_COST')->where('PK_NO',$id)->first();
              $shipment                   = $this->getShipmentInfoBykey($consignment->SHIPMENT_KEY);

              $data                       = json_decode($shipment, TRUE);

              $tracking_no                = $data['data'][$consignment->SHIPMENT_KEY]['tracking_no'];

              $shipment_cost              = $data['data'][$consignment->SHIPMENT_KEY]['effective_price'];


              if(!empty($consignment->SHIPMENT_KEY) && empty($tracking_no)){

                  $this->newCartCheckout($consignment->SHIPMENT_KEY);
                  $shipment                   = $this->getShipmentInfoBykey($consignment->SHIPMENT_KEY);

                  $data                       = json_decode($shipment, TRUE);

                  $tracking_no                = $data['data'][$consignment->SHIPMENT_KEY]['tracking_no'];

                  $shipment_cost              = $data['data'][$consignment->SHIPMENT_KEY]['effective_price'];

                  $consignment = OrderConsignment::where('SHIPMENT_KEY',$consignment->SHIPMENT_KEY)->first();

                  $consignment->POSTAGE_COST         = $shipment_cost;
                  $consignment->COURIER_TRACKING_NO  = $tracking_no;
                  $consignment->SS_CREATED_ON        = date('Y-m-d');
                  $consignment->F_SS_CREATED_BY      = Auth::user()->PK_NO;
                  $consignment->update();
              }
              else
              {

              $data                        =  OrderConsignment::find($id);

              $data->POSTAGE_COST          = $shipment_cost;

              $data->COURIER_TRACKING_NO   = $tracking_no;

              $data->SS_MODIFIED_ON        = date('Y-m-d') ;

              $data->F_SS_MODIFIED_BY      = Auth::user()->PK_NO;

              $data->update();
              }

          } catch (\Exception $e) {
                DB::rollback();
              return $this->formatResponse(false, 'Unable Update !', 'admin.product.list');
          }
      DB::commit();
      return $this->formatResponse(true, 'Consignment Note successfully Updated!', 'admin.product.create',$data);
}


public function newCartCheckout($key)
    {
            $api_key = env('POSLAJU_API_KEY');
            $data = "api_key=$api_key&shipment_keys=$key";

            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => env('POSLAJU_CHECKOUT'),
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => $data,
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
              ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
         return $response;
    }



    public function apiPostRequest($post_url,$data){
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $post_url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $data,
          CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded',
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;

    }

        //Shipment status value and label to be shown to user
    public function getShipmentStatus(){
        $api_key                = env('POSLAJU_API_KEY');
        $data                   = "api_key=$api_key";
        $post_url               = env('POSLAJU_GET_SHIPMENT');
        $response               = $this->apiPostRequest($post_url,$data);
        $data                   = json_decode($response, TRUE);
        if($data['message'] == 'success'){
            // return $this->formatResponse(true, 'Cart Data Loaded !', '', $data);
            return $data;
         }
         else{
             return false;
         }
    }

    //To get shipment details given an array of shipment_keys.
    public function getShipmentInfoBykey($shipment_keys){

       $api_key= env('POSLAJU_API_KEY');

       $data = "api_key=$api_key&shipment_keys=$shipment_keys";

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => env('POSLAJU_GET_SHIPMENT'),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $data,
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
          ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
       return $response;
    }



        public function createShipment($request){

            //$this->api_key;
          $api_key                = env('POSLAJU_API_KEY');
          // Required, value either [pickup] or [dropoff] only
          $send_method            = 'pickup';
          //2021-02-06
          $send_date              = date('Y-m-d');

          //Required, value either [parcel] or [document] only
          $type                   = 'parcel';
          // Required, between 0.001 to 30, 3 decimal point

          $declared_weight        = '5';

        //  Required, valid value are "poslaju", "ems" or "airparcel" only--

          $provider_code          = 'poslaju';
          //Required, only accept values from /get_parcel_sizes response
          //"flyers_s": "Flyers S","flyers_m": "Flyers M","flyers_l": "Flyers L","flyers_xl": "Flyers XL","envelope_third": "Envelope 1\/3 A4","envelope_a4": "Envelope A4","envelope_a5": "Envelope A5","box": "Box \/ Self Wrapped"
          $size                   = 'box';

         // Required for size [box]
          $length                 = '100';

         // Required for size [box]
          $width                  = '20';

          //Required for size [box]
          $height                 = '10';

          //Required, only accept values from /get_content_types endpoint
          $content_type           = 'general';

          //Required
          $content_description    = 'test content description';

          //Required, number only without currency code, ie 200 for RM 200.00
          $content_value          = '200';

          //Required
          $sender_name            = 'AZURAMART';

          //Required
          $sender_phone           = '+60174258105';

          $sender_email           = 'azuramart@gmail.com';

          $sender_company_name    = 'AZURAMART';

          //Required
          $sender_address_line_1  = 'NO. 8 PINTAS TUNA 3,SEBERANG JAYA';

          $sender_address_line_2  = NULL;

          $sender_address_line_3  = NULL;

          $sender_address_line_4  = NULL;

          //Required
          $sender_postcode        = '13700';

          $sender_city            = 'PERAI';

          $sender_state           = 'PULAU PINANG';

          $sender_country_code    = 'MY';

          //Required
          $receiver_name          = 'Maidul Islam';
          //Required
          $receiver_phone         = '+60126920347';

          //Required
          $receiver_email         = 'maidul@email.com';

          $receiver_company_name  = NULL;

          //Required
          $receiver_address_line_1 ='No.16 BK5D/4D,Bandar Kinrara';

          $receiver_address_line_2 =  NULL;

          $receiver_address_line_3 = NULL;

          $receiver_address_line_4 = NULL;

          //Required for domestic
          $receiver_postcode       = '47180';

          $receiver_city           = 'Puchong';

          $receiver_state          = 'Selangor';
          //Requried
          $receiver_country_code   = 'MY';

          //Post Data
          $data = "api_key=$api_key&send_method=$send_method&send_date=$send_date&type=$type&declared_weight=$declared_weight&provider_code=$provider_code&size=$size&length=$length&width=$width&height=$height&content_type=$content_type&content_description=$content_description&content_value=$content_value&sender_name=$sender_name&sender_phone=$sender_phone&sender_address_line_1=$sender_address_line_1&sender_postcode=$sender_postcode&receiver_name=$receiver_name&receiver_phone=$receiver_phone&receiver_email=$receiver_email&receiver_address_line_1=$receiver_address_line_1&receiver_postcode=$receiver_postcode&receiver_country_code=$receiver_country_code";

          $post_url = env('POSLAJU_SHIPMENT');

          $response = $this->apiPostRequest($post_url,$data);


      return $response;
      }

    //Checkout shipments to get download connote links.
    public function cartCheckout($key)
    {

        $api_key = env('POSLAJU_API_KEY');

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => env('POSLAJU_CHECKOUT'),
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => array('api_key' => $api_key,'shipment_keys' => '".$key."'),
          CURLOPT_HTTPHEADER => array(
          ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
       return $response;

    }



//Get all pending cartlist //To get all shipment that is ready to checkout.

public function getCartList($request){

    $api_key    = env('POSLAJU_API_KEY');

    $data       = "api_key=$api_key";

    $post_url   = 'http://sendparcel-test.ap-southeast-1.elasticbeanstalk.com/apiv1/get_cart_items';

    $data       = $this->apiPostRequest($post_url,$data);

    $data       = json_decode($data, TRUE);

    if($data['message'] == 'success'){
       // return $this->formatResponse(true, 'Cart Data Loaded !', '', $data);

       return $data;
    }
    else{
        return $this->formatResponse(false, 'Cart Data Loaded !', '', $data);
    }

}



    public function getPaginatedList($request, int $per_page = 5)
    {
        $data =  $this->dispatch->get();
        return $this->formatResponse(true, 'Data found successfully !', 'admin.dispatched.list', $data);
    }


    public function getOrderForDisopatch($PK_NO)
    {
        $data       = array();

        $booking    = $this->booking->where('PK_NO',$PK_NO)
        ->first();
        $data['booking']            = $booking;
        return $this->formatResponse(true, 'Data found successfully !', 'admin.booking.list', $data);
    }





}
