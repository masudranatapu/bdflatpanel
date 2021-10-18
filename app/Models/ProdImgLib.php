<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class ProdImgLib extends Model
{
    protected $table        = 'PRD_IMG_LIBRARY';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;
    // const CREATED_AT     = 'create_dttm';
    // const UPDATED_AT     = 'update_dttm';


    protected $fillable = [
        'F_PRD_MASTER_NO', 'F_PRD_VARIANT_NO'
    ];

  

    
}
