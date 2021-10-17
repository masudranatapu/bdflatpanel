<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    protected $table = 'SC_COURIER';

    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO','CODE'];

    // const CREATED_AT = 'create_dttm';
    // const UPDATED_AT = 'update_dttm';


}
