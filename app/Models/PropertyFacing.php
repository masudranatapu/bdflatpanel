<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyFacing extends Model
{
    protected $table = 'PRD_PROPERTY_FACING';
    protected $primaryKey = 'PK_NO';
    protected $fillable = ['TITLE'];
    public $timestamps = false;

    public function getPropertyFacing()
    {
        return PropertyFacing::where('IS_ACTIVE', 1)->pluck('TITLE', 'PK_NO');
    }
}
