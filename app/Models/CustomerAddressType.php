<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddressType extends Model
{
    protected $table 		= 'SLS_CUSTOMER_ADDRESS_TYPE';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable = [
        'NAME'
    ];

    public function getAddTypeCombo(){
        return CustomerAddressType::where('IS_ACTIVE', 1)->pluck('NAME', 'PK_NO');
    }

    public function address() {
        return $this->belongsTo('App\Models\CustomerAddress', 'F_ADDRESS_TYPE_NO', 'PK_NO');
    }
}
