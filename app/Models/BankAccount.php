<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    protected $table 		= 'ACC_BANK_ACC';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;
    // const CREATED_AT     = 'create_dttm';
    // const UPDATED_AT     = 'update_dttm';

    protected $fillable = [
        'NAME'
    ];

    public function AccountSource() {
        return $this->belongsTo('App\Models\AccountSource')->orderBy('NAME','ASC');
    }

    public function getAllBankAcc($acc_source_id = null, $type=null){
        $response   = '';
        if($type == 'combo'){            
            if ($acc_source_id != null) {
                $data = BankAccount::where('F_ACCOUNT_SOURCE_NO',$acc_source_id)->get();
            }else{
                $data  = BankAccount::get();
            }
            if ($data && $data->count() > 0 ) {
                foreach ($data as $value) {
                    $CODE = '';
                    if($value->CODE){ $CODE = $value->CODE;}
                    $response .= '<option value="'.$value->PK_NO.'"  title="'.$value->CODE.'">'.$value->NAME.'</option>';
                }
            }else{
                $response .= '<option value="">No data found</option>';
            }
        }else{
            if ($acc_source_id != null) {
                $response = BankAccount::where('F_ACCOUNT_SOURCE_NO',$acc_source_id)::pluck('NAME', 'PK_NO');
            }else{
                $response = BankAccount::pluck('NAME', 'PK_NO');
            }

             
        }

        return $response;
    }
}
