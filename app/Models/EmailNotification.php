<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class EmailNotification extends Model
{
    protected $table = 'SLS_NOTIFICATION_EMAIL';

    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO'];

    const CREATED_AT = 'SS_CREATED_ON';
    const UPDATED_AT = 'SS_MODIFIED_ON';


//     public static function boot()
//     {
//        parent::boot();
//        static::creating(function($model)
//        {
//            $user = Auth::user();
//            $model->F_SS_CREATED_BY = $user->PK_NO;
//        });

//        static::updating(function($model)
//        {
//            $user = Auth::user();
//            $model->F_SS_MODIFIED_BY = $user->PK_NO;
//        });
//    }

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
