<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Box extends Model
{
    protected $table = 'SC_BOX';

    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO'];

	// const CREATED_AT = 'create_dttm';
    // const UPDATED_AT = 'update_dttm';

    public function warehouse() {
        return $this->hasOne('App\Models\Warehouse', 'PK_NO', 'F_INV_WAREHOUSE_NO');
    }

    public function box_serial() {
        return $this->hasOne('App\Models\Shipmentbox', 'F_BOX_NO', 'PK_NO')->orderBy('BOX_SERIAL','ASC');
    }

    public function shipment_box_no() {
        return $this->hasOne('App\Models\Shipmentbox', 'F_BOX_NO', 'PK_NO');
    }
}
