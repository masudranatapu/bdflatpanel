<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountMethod extends Model
{
    protected $table 		= 'ACC_PAYMENT_METHODS';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;
    // const CREATED_AT     = 'create_dttm';
    // const UPDATED_AT     = 'update_dttm';

    protected $fillable = [
        'NAME'
    ];

    public function AccountSource() {
        return $this->belongsTo('App\Models\AccountSource');
    }


    public function getAllPaymentMethod($acc_source_id = null, $type=null){
        $response   = '';
        if($type == 'combo'){
            if ($acc_source_id != null) {
                $data = AccountMethod::where('F_ACC_SOURCE_NO',$acc_source_id)->where('IS_ACTIVE',1)->get();
            }else{
                $data  = AccountMethod::get();
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
                $response = AccountMethod::where('IS_ACTIVE',1)->where('F_ACC_SOURCE_NO',$acc_source_id)->pluck('NAME', 'PK_NO');

            }else{
                $response = AccountMethod::where('IS_ACTIVE',1)->pluck('NAME', 'PK_NO');
            }


        }

        return $response;
    }







}
