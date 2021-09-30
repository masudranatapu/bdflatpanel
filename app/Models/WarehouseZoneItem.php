<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseZoneItem extends Model
{
    protected $table = 'INV_WAREHOUSE_ZONE_STOCK_ITEM';
    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';
}
