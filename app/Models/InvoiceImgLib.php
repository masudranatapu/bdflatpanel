<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class InvoiceImgLib extends Model
{
    protected $table        = 'PRC_IMG_LIBRARY';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;
    // const CREATED_AT     = 'create_dttm';
    // const UPDATED_AT     = 'update_dttm';

    protected $fillable = [
        'F_INV_STOCK_IN_NO'
    ];
}
