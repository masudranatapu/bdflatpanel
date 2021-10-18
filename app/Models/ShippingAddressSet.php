<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddressSet extends Model
{
    protected $table        = 'SC_SHIPING_ADDRESS_SET';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = ['NAME'];





}
