<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class SmsNotification extends Model
{
    protected $table = 'SLS_NOTIFICATION';

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




}
