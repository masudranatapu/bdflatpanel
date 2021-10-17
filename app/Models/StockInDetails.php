<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInDetails extends Model
{
    protected $table = 'PRC_STOCK_IN_DETAILS';

    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO','CODE'];

	// const CREATED_AT = 'create_dttm';
	// const UPDATED_AT = 'update_dttm';
}
