<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentSign extends Model
{
    protected $table        = 'SC_SIGNATURE';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable = ['NAME'];

    

    

}
