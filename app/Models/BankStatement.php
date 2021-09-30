<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankStatement extends Model
{
    protected $table 		= 'ACC_BANK_TXN_STATEMENT';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;
    // const CREATED_AT     = 'create_dttm';
    // const UPDATED_AT     = 'update_dttm';

    protected $fillable = ['TXN_DATE','F_ACC_BANK_PAYMENT_NO'];

    public function bank() {
        return $this->belongsTo('App\Models\PaymentBankAcc', 'F_ACC_BANK_PAYMENT_NO', 'PK_NO');
    }

    public function payment() {
        return $this->belongsTo('App\Models\AccBankTxn', 'F_ACC_BANK_TXN_NO', 'PK_NO');
    }





}

