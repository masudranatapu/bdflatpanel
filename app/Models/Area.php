<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'SS_AREA';
    protected $primaryKey = 'PK_NO';
    public $timestamps = false;

    public function getArea($id)
    {
        return Area::where('F_CITY_NO', $id)->whereNull('F_PARENT_AREA_NO')->pluck('AREA_NAME', 'PK_NO');
    }


}
