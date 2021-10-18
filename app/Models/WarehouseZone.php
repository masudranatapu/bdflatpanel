<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseZone extends Model
{
    protected $table = 'INV_WAREHOUSE_ZONES';
    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';


    public function warehouse() {
        return $this->hasOne('App\Models\Warehouse', 'PK_NO', 'F_INV_WAREHOUSE_NO');
    }
}
