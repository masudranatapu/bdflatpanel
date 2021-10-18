<?php

namespace App\Models;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PaymentExfer extends Model
{
    protected $table 		= 'ACC_PAYMENT_BANK_ACC_EXFER';
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

    public function ex_account()
    {
        return $this->belongsTo('App\Models\PaymentBank', 'F_I_ACC_PAYMENT_BANK_ACC_NO');
    }

    public function account_head()
    {
        return $this->belongsTo('App\Models\PartyPaymentMethodHead', 'F_ACC_PAYMENT_ACC_HEAD_NO');
    }

    public function approveBy()
    {
        return $this->belongsTo('App\Models\Auth', 'F_VERIFIED_BY_SA_USER_NO');
    }

    public function entryBy()
    {
        return $this->belongsTo('App\Models\Auth', 'SS_CREATED_BY');
    }
}
