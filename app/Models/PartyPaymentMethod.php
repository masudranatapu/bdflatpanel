<?php

namespace App\Models;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PartyPaymentMethod extends Model
{
    protected $table 		= 'ACC_PARTY_PAYMENT_METHOD';
	protected $primaryKey 	= 'PK_NO';
    const CREATED_AT     	= 'SS_CREATED_ON';
    const UPDATED_AT     	= 'SS_MODIFIED_ON';

    protected $fillable 	= ['PK_NO','CODE'];



    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            $user = Auth::user();
            $model->SS_CREATED_BY = $user->PK_NO;
        });

        static::updating(function($model)
        {
            $user = Auth::user();
            $model->F_SS_MODIFIED_BY = $user->PK_NO;
        });
    }
}
