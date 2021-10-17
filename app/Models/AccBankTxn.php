<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Model;

class AccBankTxn extends Model
{
    protected $table        = 'ACC_BANK_TXN';
    protected $primaryKey   = 'PK_NO';
    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';


    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['TXN_REF'];

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

    public function createdBy() {
        return $this->belongsTo('App\User', 'F_SS_CREATED_BY', 'PK_NO');
    }

    public function customerPayment() {
        return $this->belongsTo('App\Models\PaymentCustomer', 'F_CUSTOMER_PAYMENT_NO', 'PK_NO');
    }

    public function resellerPayment() {
        return $this->belongsTo('App\Models\PaymentReseller', 'F_RESELLER_PAYMENT_NO', 'PK_NO');
    }


    public function bank()
    {
        return $this->belongsTo('App\Models\PaymentBank', 'F_ACC_PAYMENT_BANK_NO');
    }


    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'F_CUSTOMER_NO');
    }

    public function reseller()
    {
        return $this->belongsTo('App\Models\Reseller', 'F_RESELLER_NO');
    }






}
