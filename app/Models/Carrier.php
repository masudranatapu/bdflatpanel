<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $table = 'SC_CARRIER';

    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO','CODE'];

	// const CREATED_AT = 'create_dttm';
	// const UPDATED_AT = 'update_dttm';


}
