<?php

namespace App\Models;

use App\Models\BankAccount;
use Illuminate\Database\Eloquent\Model;

class AccountSource extends Model
{
    protected $table 		= 'ACC_PAYMENT_BANK_ACC';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;
    // const CREATED_AT     = 'create_dttm';
    // const UPDATED_AT     = 'update_dttm';

    protected $fillable = [
        'BANK_NAME','BANK_ACC_NAME','BANK_ACC_NO'
    ];

    public function getAllSource($type=null){

        $data = AccountSource::get();

        if($type == 'combo'){
            $response = '';

            if ($data) {
                foreach ($data as $value) {
                    $CODE = '';
                    if($value->CODE){
                        $CODE = $value->CODE;
                    }
                    $response .= '<option value="'.$value->PK_NO.'"  title="'.$value->CODE.'">'.$value->NAME.'</option>';
                    }
            }else{
                $response .= '<option value="">No data found</option>';
            }
        }else{
            $response = [];
            if ($data) {
                foreach ($data as $key => $value) {
                    $response[$value->PK_NO] = $value->NAME;
                }
            }
        }


        return $response;
    }


    public function bankAccount() {
        return $this->hasMany('App\Models\BankAccount', 'F_ACCOUNT_SOURCE_NO', 'PK_NO');
    }

    public function paymentMethod() {
        return $this->hasMany('App\Models\AccountMethod', 'F_ACC_SOURCE_NO', 'PK_NO');
    }

    public function bankAccountActive() {
        return $this->hasMany('App\Models\BankAccount', 'F_ACCOUNT_SOURCE_NO', 'PK_NO')->where('IS_ACTIVE', 1);
    }

    public function paymentMethodActive() {
        return $this->hasMany('App\Models\AccountMethod', 'F_ACC_SOURCE_NO', 'PK_NO')->where('IS_ACTIVE', 1);
    }
}
