<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $table 		= 'SLS_CUSTOMERS_ADDRESS';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable = [
        'NAME','F_ADDRESS_TYPE_NO'
    ];

    public function addressType() {
        return $this->hasOne('App\Models\CustomerAddressType', 'PK_NO', 'F_ADDRESS_TYPE_NO');
    }

    public function country() {
        return $this->hasOne('App\Models\Country', 'PK_NO', 'F_COUNTRY_NO');
    }

    public function state() {
        return $this->hasOne('App\Models\State', 'PK_NO', 'STATE');
    }

    public function city() {
        return $this->hasOne('App\Models\City', 'PK_NO', 'CITY');
    }

}
