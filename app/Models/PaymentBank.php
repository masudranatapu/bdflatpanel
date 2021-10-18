<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentBank extends Model
{
    protected $table 		= 'ACC_PAYMENT_BANK_ACC';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;
    // const CREATED_AT     = 'create_dttm';
    // const UPDATED_AT     = 'update_dttm';

    protected $fillable = ['BANK_NAME','BANK_ACC_NAME'];


}

