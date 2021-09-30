<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table 		= 'SLS_ORDER';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable = [
        'NAME'
    ];


    public function booking()
    {
        return $this->belongsTo('App\Models\Booking', 'F_BOOKING_NO');
    }

    public function fromAddress()
    {
        return $this->belongsTo('App\Models\CustomerAddress', 'F_FROM_ADDRESS');
    }

    public function from_country()
    {
        return $this->hasOne('App\Models\Country','NAME', 'FROM_COUNTRY');
    }

    public function to_country()
    {
        return $this->hasOne('App\Models\Country','NAME', 'DELIVERY_COUNTRY');
    }

    public function dispatch()
    {
        return $this->hasMany('App\Models\Dispatch','F_ORDER_NO', 'PK_NO');
    }

    public function consignment()
    {
        return $this->hasMany('App\Models\OrderConsignment','F_ORDER_NO', 'PK_NO');
    }

    public function bookingDetails()
    {
        return $this->hasMany('App\Models\BookingDetails','F_BOOKING_NO', 'F_BOOKING_NO');
    }


}
