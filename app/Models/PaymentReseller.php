<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class PaymentReseller extends Model
{
    protected $table = 'ACC_RESELLER_PAYMENTS';

    protected $primaryKey   = 'PK_NO';
    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';
    protected $fillable     = ['CODE', 'RESELLER_NAME'];

    private $user_id;
    public static function boot()
        {
           parent::boot();
           static::creating(function($model)
           {
               $user = Auth::user();
               $model->F_SS_CREATED_BY = $user->PK_NO ?? $model->getsetApiAuthId();
           });

           static::updating(function($model)
           {
               $user = Auth::user();
               $model->F_SS_MODIFIED_BY = $user->PK_NO ?? $model->getsetApiAuthId();
           });
       }

    public function setApiAuthId( $user_id )
    {
        $this->user_id = $user_id;
    }

    public function getsetApiAuthId()
    {
        return $this->user_id;
    }

    public function entryBy()
    {
        return $this->belongsTo('App\Models\Auth', 'F_SS_CREATED_BY');
    }

    public function reseller()
    {
        return $this->belongsTo('App\Models\Reseller', 'F_RESELLER_NO');
    }
    public function bankTxn() {
        return $this->hasOne('App\Models\AccBankTxn', 'F_RESELLER_PAYMENT_NO', 'PK_NO');
    }


    public function allOrderPayments() {

        return $this->hasMany('App\Models\OrderPayment', 'F_ACC_RESELLER_PAYMENT_NO', 'PK_NO');
    }







}
