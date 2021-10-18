<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $table 		= 'SC_ORDER_DISPATCH';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    const CREATED_AT        = 'CREATED_AT';

    protected $fillable     = ['F_ORDER_NO'];


    public function order()
    {
        return $this->belongsTo('App\Models\Order', 'F_ORDER_NO');
    }
    public function courier()
    {
        return $this->belongsTo('App\Models\Courier', 'F_COURIER_NO');
    }

    public function allChild()
    {
        return $this->hasMany('App\Models\DispatchDetails', 'F_SC_ORDER_DISPATCH_NO', 'PK_NO');
    }



}
