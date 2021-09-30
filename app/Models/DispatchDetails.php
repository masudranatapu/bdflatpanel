<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchDetails extends Model
{
    protected $table 		= 'SC_ORDER_DISPATCH_DETAILS';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable     = ['F_ORDER_NO'];


    public function booking_details()
    {
        return $this->belongsTo('App\Models\BookingDetails', 'F_BOOKING_DETAILS_NO');
    }


}
