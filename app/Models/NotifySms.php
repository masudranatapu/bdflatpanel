<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifySms extends Model
{
    protected $table 		= 'SLS_NOTIFICATION';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable = ['BODY'];


    public function customer() {
        return $this->belongsTo('App\Models\Customer', 'CUSTOMER_NO', 'PK_NO');
    }

    public function reseller() {
        return $this->belongsTo('App\Models\Reseller', 'RESELLER_NO', 'PK_NO');
    }

    public function booking() {
        return $this->belongsTo('App\Models\Booking', 'F_BOOKING_NO', 'PK_NO');
    }


}
