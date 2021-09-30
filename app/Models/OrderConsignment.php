<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderConsignment extends Model
{
    protected $table 		= 'SC_ORDER_CONSIGNMENT';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable = ['F_ORDER_NO'];





}
