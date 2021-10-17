<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    protected $table = 'SC_SHIPMENT';

    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO'];

	// const CREATED_AT = 'create_dttm';
    // const UPDATED_AT = 'update_dttm';

    public function from_warehouse() {
        return $this->hasOne('App\Models\Warehouse', 'PK_NO', 'F_FROM_INV_WAREHOUSE_NO');
    }

    public function to_warehouse() {
        return $this->hasOne('App\Models\Warehouse', 'PK_NO', 'F_TO_INV_WAREHOUSE_NO');
    }

    public function fromAddress() {
        return $this->hasOne('App\Models\ShippingAddress', 'PK_NO', 'F_FROM_ADDRESS');
    }

    public function toAddress() {
        return $this->hasOne('App\Models\ShippingAddress', 'PK_NO', 'F_SHIP_TO_ADDRESS');
    }
    public function billAddress() {
        return $this->hasOne('App\Models\ShippingAddress', 'PK_NO', 'F_BILL_TO_ADDRESS');
    }

    public function shippingAddress() {
        return $this->hasOne('App\Models\ShippingAddress', 'PK_NO', 'F_SHIPPING_AGENT');
    }

    public function receivingAddress() {
        return $this->hasOne('App\Models\ShippingAddress', 'PK_NO', 'F_RECIEVING_AGENT');
    }

    public function signature() {
        return $this->hasOne('App\Models\ShipmentSign', 'PK_NO', 'F_SIGNATURE');
    }

    public function shipment_address_set_ship_to() {
        return $this->hasOne('App\Models\ShippingAddressSet', 'F_SHIPPMENT_NO','PK_NO')->where('ADDRESS_TYPE','Ship_to');
    }

    public function shipment_address_set_from() {
        return $this->hasOne('App\Models\ShippingAddressSet', 'F_SHIPPMENT_NO','PK_NO')->where('ADDRESS_TYPE','From');
    }

    public function shipment_address_set_agent() {
        return $this->hasOne('App\Models\ShippingAddressSet', 'F_SHIPPMENT_NO','PK_NO')->where('ADDRESS_TYPE','Shipping_agent');
    }

    public function shipment_address_set_receiver() {
        return $this->hasOne('App\Models\ShippingAddressSet', 'F_SHIPPMENT_NO','PK_NO')->where('ADDRESS_TYPE','Receiving_agent');
    }
}
