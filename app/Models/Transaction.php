<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    protected $table = 'ACC_CUSTOMER_TRANSACTION';

    protected $primaryKey   = 'PK_NO';
    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';
    protected $fillable     = ['CODE', 'CUSTOMER_NAME'];

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

    public function payment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
       return $this->belongsTo('App\Models\PaymentCustomer', 'F_CUSTOMER_PAYMENT_NO', 'PK_NO');
    }

    public function customer(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
       return $this->belongsTo('App\Models\Owner', 'F_CUSTOMER_NO', 'PK_NO');
    }




}
