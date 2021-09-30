<?php

namespace App\Models;

use Auth;
use App\Models\Customer;
use App\Models\Reseller;
use App\Models\CustomerAddress;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table 		= 'SLS_BOOKING';
    protected $primaryKey   = 'PK_NO';
    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';

    protected $fillable = [
        'BOOKING_NO'
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
           $user = Auth::user();
           $model->F_SS_CREATED_BY = $user->PK_NO;
        });

        static::updating(function($model)
        {
           $user = Auth::user();
           $model->F_SS_MODIFIED_BY = $user->PK_NO;
        });
    }

    public function booking_details() {
        return $this->hasMany('App\Models\BookingDetails', 'F_BOOKING_NO');
    }

    public function booking_details_returned() {
        return $this->hasMany('App\Models\BookingDetailsAud', 'F_BOOKING_NO')->where('CHANGE_TYPE', 'ORDER_RETURN');
    }

    public function createdBy() {
        return $this->belongsTo('App\User', 'F_SS_CREATED_BY', 'PK_NO');
    }

    public function getCustomer() {
        return $this->belongsTo('App\Models\Customer','F_CUSTOMER_NO','PK_NO');
    }

    public function getReseller() {
        return $this->belongsTo('App\Models\Reseller', 'F_RESELLER_NO', 'PK_NO');
    }


    public function getAgent() {
        return $this->belongsTo('App\Models\Agent', 'F_BOOKING_SALES_AGENT_NO', 'PK_NO');
    }

    public function getOrder() {
        return $this->hasOne('App\Models\Order', 'F_BOOKING_NO', 'PK_NO');
    }

    public function bookingCreatedBy() {
        return $this->belongsTo('App\Models\Auth', 'F_SS_CREATED_BY','PK_NO');
    }

    public function country() {
        return $this->belongsTo('App\Models\Country', 'F_COUNTRY_NO', 'PK_NO');
    }
    public function cancelBy() {
        return $this->belongsTo('App\Models\Auth', 'CANCEL_REQUEST_BY', 'PK_NO');
    }

    public function to_country()
    {
        return $this->hasOne('App\Models\Country','NAME', 'DELIVERY_COUNTRY');
    }

    public function getCustomerPostCode($customer_no,$reseller_no,$type) {

        if ($type == 0) {
            $post_code = CustomerAddress::select('PK_NO','POST_CODE')->where('F_CUSTOMER_NO',$customer_no)->where('F_ADDRESS_TYPE_NO',1)->first();
        }else{
            $post_code = Reseller::select('PK_NO','POST_CODE')->where('PK_NO',$reseller_no)->first();
        }
        // return $customer_no.$type;
        return $post_code;
    }



    public function getAgentCombo(){
        return Agent::where('IS_ACTIVE', 1)->pluck('NAME', 'PK_NO');
    }

    public function getCustomerCombo(Type $var = null)
    {
        $response = '';
        $data = Customer::select('NAME','PK_NO')->where('IS_ACTIVE', 1)->get();

        if ($data && count($data) > 0 ) {
            foreach ($data as $value) {
                $response .= '<option value="'.$value->PK_NO.'">'.$value->NAME.'</option>';
            }
        }else{
            $response .= '<option value="">No data found</option>';
        }
        return $response;
    }

    public function bokingDetails()
    {
        return $this->hasMany('App\Models\BookingDetails', 'F_BOOKING_NO')->orderBy('PK_NO','ASC');
    }

    public function getCustomerAddress($customer_id,$type)
    {
        $customer_info   = CustomerAddress::select('SLS_CUSTOMERS_ADDRESS.*','c.NAME as COUNTRY','s.STATE_NAME as STATE','city.CITY_NAME as CITY')
        ->leftjoin('SS_COUNTRY as c','c.PK_NO','SLS_CUSTOMERS_ADDRESS.F_COUNTRY_NO')
        ->leftjoin('SS_STATE as s','s.PK_NO','SLS_CUSTOMERS_ADDRESS.STATE')
        ->leftjoin('SS_CITY as city','city.PK_NO','SLS_CUSTOMERS_ADDRESS.CITY')
        ->where('F_ADDRESS_TYPE_NO',$type)
        ->where('F_CUSTOMER_NO',$customer_id)
        ->get();
        return $customer_info;
    }

    public static function getResellerAddress($customer_id)
    {
        $customer_info   = Reseller::select('SLS_RESELLERS.*','c.NAME as COUNTRY','s.STATE_NAME as STATE','city.CITY_NAME as CITY')
        ->leftjoin('SS_COUNTRY as c','c.PK_NO','SLS_RESELLERS.F_COUNTRY_NO')
        ->leftjoin('SS_STATE as s','s.PK_NO','SLS_RESELLERS.STATE')
        ->leftjoin('SS_CITY as city','city.PK_NO','SLS_RESELLERS.CITY')
        ->where('SLS_RESELLERS.PK_NO',$customer_id)->get();
        return $customer_info;
    }

    public static function getCustomerAddressOne($delivery_address,$type)
    {
        $customer_info   = CustomerAddress::select('SLS_CUSTOMERS_ADDRESS.*','c.NAME as COUNTRY','s.STATE_NAME as STATE','city.CITY_NAME as CITY')
        ->leftjoin('SS_COUNTRY as c','c.PK_NO','SLS_CUSTOMERS_ADDRESS.F_COUNTRY_NO')
        ->leftjoin('SS_STATE as s','s.PK_NO','SLS_CUSTOMERS_ADDRESS.STATE')
        ->leftjoin('SS_CITY as city','city.PK_NO','SLS_CUSTOMERS_ADDRESS.CITY')
        ->where('F_ADDRESS_TYPE_NO',$type)
        ->where('SLS_CUSTOMERS_ADDRESS.PK_NO',$delivery_address)
        ->first();
        return $customer_info;
    }
}
