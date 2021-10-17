<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccResellerCustomerTnx extends Model
{
    protected $table 		= 'ACC_RESELLER_CUSTOMER_TX';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;
    // const CREATED_AT     = 'create_dttm';
    // const UPDATED_AT     = 'update_dttm';

    protected $fillable = ['PK_NO'];


}
