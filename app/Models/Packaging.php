<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Packaging extends Model
{
    protected $table = 'SC_PACKAGING_LIST';

    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO'];

	// const CREATED_AT = 'create_dttm';
    // const UPDATED_AT = 'update_dttm';



    

    
}
