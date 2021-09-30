<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoxType extends Model
{
    protected $table = 'SC_BOX_TYPE';

    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO'];
}
