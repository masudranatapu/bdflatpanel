<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipmentbox extends Model
{
    protected $table = 'SC_SHIPMENT_BOX';

    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO'];

	// const CREATED_AT = 'create_dttm';
    // const UPDATED_AT = 'update_dttm';

    public function SC_BOX() {
        return $this->hasOne('App\Models\Box', 'PK_NO', 'F_BOX_NO');
    }
}
