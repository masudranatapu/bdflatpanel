<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class OrderRtc extends Model
{
    protected $table 		= 'SLS_ORDER_RTC';
    protected $primaryKey   = 'PK_NO';
    const CREATED_AT     	= 'SS_CREATED_ON';
    const UPDATED_AT     	= 'SS_MODIFIED_ON';

    protected $fillable = ['F_ORDER_NO'];

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


    public function booking()
    {
        return $this->belongsTo('App\Models\Booking', 'F_BOOKING_NO');
    }

    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'F_ORDER_NO');
    }


}
