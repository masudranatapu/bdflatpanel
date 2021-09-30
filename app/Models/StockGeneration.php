<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockGeneration extends Model
{
    protected $table = 'INV_STOCK_PRC_STOCK_IN_MAP';

    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO','F_PRC_STOCK_IN_NO','F_INV_WAREHOUSE_NO'];
    public $timestamps 		= false;

	// const CREATED_AT = 'create_dttm';
	// const UPDATED_AT = 'update_dttm';
}
