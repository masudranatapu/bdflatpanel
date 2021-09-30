<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $table        = 'SLS_CUSTOMER_ADDRESS_TYPE';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CODE', 'NAME'
    ];

    

    

}
