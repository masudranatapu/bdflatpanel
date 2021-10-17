<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class PropertyCondition extends Model
{
    protected $table = 'PRD_PROPERTY_CONDITION';
    protected $primaryKey = 'PK_NO';
    public $timestamps = false;

    public function getPropertyCondition()
    {
        return PropertyCondition::where('IS_ACTIVE', 1)->pluck('PROD_CONDITION', 'PK_NO');
    }


}
