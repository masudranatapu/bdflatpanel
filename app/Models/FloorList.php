<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FloorList extends Model
{
    public $timestamps = false;
    protected $table = 'PRD_FLOOR_LIST';
    protected $primaryKey = 'PK_NO';
    protected $fillable = ['NAME', 'IS_ACTIVE'];

    public function getFloorList() {
        return FloorList::where('IS_ACTIVE', 1)->pluck('NAME', 'PK_NO');
    }
}
