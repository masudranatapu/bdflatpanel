<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class BookingDetailsTemp extends Model
{
    protected $table 		= 'SLS_BOOKING_DETAILS_TEMP';
    protected $primaryKey   = 'PK_NO';
    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';

    protected $fillable = [ 'F_BOOKING_NO'];

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

    public function booking() {
        return $this->belongsTo('App\Models\BookingTemp', 'F_BOOKING_NO', 'PK_NO');
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



}
