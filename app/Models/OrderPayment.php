<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $table 		= 'ACC_ORDER_PAYMENT';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable = ['ORDER_NO'];


    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'ORDER_NO');
    }

    public function customerPayment()
    {
        return $this->belongsTo('App\Models\PaymentCustomer', 'F_ACC_CUSTOMER_PAYMENT_NO');
    }






}
