<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class BookingDetailsAud extends Model
{
    protected $table 		= 'SLS_BOOKING_DETAILS_AUD';
    protected $primaryKey   = 'PK_NO';
    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';

    protected $fillable = [ 'F_BOOKING_NO'];


    public function booking() {
        return $this->belongsTo('App\Models\Booking', 'F_BOOKING_NO', 'PK_NO');
    }

    public function consignment() {
        return $this->hasOne('App\Models\DispatchDetails', 'F_BOOKING_DETAILS_NO', 'PK_NO');
    }

    public function bundle() {
        return $this->belongsTo('App\Models\Offer', 'F_BUNDLE_NO', 'PK_NO');
    }

    public function stock() {
        return $this->belongsTo('App\Models\Stock', 'F_INV_STOCK_NO', 'PK_NO');
    }


    public function deliveryAddress() {
        return $this->belongsTo('App\Models\CustomerAddress','CURRENT_F_DELIVERY_ADDRESS', 'PK_NO');
    }

    public function getAgentCombo(){
        return Agent::where('IS_ACTIVE', 1)->pluck('NAME', 'PK_NO');
    }


    public function reseller() {
        return $this->hasMany('App\Models\Reseller', 'F_AGENT_NO', 'PK_NO');
    }

    public function customer() {
        return $this->hasMany('App\Models\Customer', 'F_SALES_AGENT_NO', 'PK_NO');
    }
}
