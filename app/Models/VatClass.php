<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VatClass extends Model
{
    protected $table = 'ACC_VAT_CLASS';
    protected $primaryKey  = 'PK_NO';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CODE', 'NAME', 'RATE'
    ];

    public function getVatClassCombo(){

    	$data = VatClass::get();
    	$response = [];
    	if ($data) {
    		foreach ($data as $key => $value) {
    			$response[$value->PK_NO] = $value->NAME;
    		}
    	}
    	return $response;
    }



}
