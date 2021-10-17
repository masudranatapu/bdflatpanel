<?php

namespace App\Models;

use Auth;
use App\Models\Customer;
use App\Models\Reseller;
use App\Models\CustomerAddress;
use Illuminate\Database\Eloquent\Model;

class BookingTemp extends Model
{
    protected $table 		= 'SLS_BOOKING_TEMP';
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


}
