<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $table = 'SS_CURRENCY';

    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO','CODE'];

	// const CREATED_AT = 'create_dttm';
	// const UPDATED_AT = 'update_dttm';

	public function getCurrencyCombo(){

        $data = Currency::get();
        $response = '';
        
        $response = [];
        	if ($data) {
        		foreach ($data as $key => $value) {
        			$response[$value->PK_NO] = $value->NAME;
        		}
        	}
        	return $response;

    }
}
