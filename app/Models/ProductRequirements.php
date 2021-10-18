<?php

namespace App\Models;

use App\Traits\RepoResponse;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductRequirements extends Model
{
    use RepoResponse;

    protected $table = 'PRD_REQUIREMENTS';
    protected $primaryKey = 'PK_NO';
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'MODIFIED_AT';
    protected $fillable = [
        'F_CITY_NO',
        'F_AREAS',
        'CITY_NAME',
        'AREA_NAMES',
        'PROPERTY_FOR',
        'F_PROPERTY_TYPE_NO',
        'PROPERTY_TYPE',
        'MIN_SIZE',
        'MAX_SIZE',
        'MIN_BUDGET',
        'MAX_BUDGET',
        'BEDROOM',
        'PROPERTY_CONDITION',
        'REQUIREMENT_DETAILS',
        'PREP_CONT_TIME',
        'EMAIL_ALERT',
        'CREATED_AT',
        'CREATED_BY',
        'MODIFIED_AT',
        'MODIFIED_BY',
        'IS_VERIFIED',
        'IS_ACTIVE',
        'F_VERIFIED_BY',
        'VERIFIED_AT',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->CREATED_BY = Auth::id();
        });

        static::updating(function ($model) {
            $model->MODIFIED_BY = Auth::id();
        });
    }

}
