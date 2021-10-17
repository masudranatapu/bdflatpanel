<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NearBy extends Model
{
    public $timestamps = false;
    protected $table = 'PRD_NEARBY';
    protected $primaryKey = 'PK_NO';
    protected $fillable = ['TITLE', 'URL_SLUG', 'IS_ACTIVE', 'ORDER_ID'];

    public function getNearBy()
    {
        return NearBy::where('IS_ACTIVE', 1)->pluck('TITLE', 'PK_NO');
    }
}
