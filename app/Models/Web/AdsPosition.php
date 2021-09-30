<?php

namespace App\Models\Web;

use App\Traits\RepoResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class AdsPosition extends Model
{
    use RepoResponse;

    protected $table = 'PRD_AD_POSITION';
    protected $primaryKey = 'PK_NO';
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'MODIFIED_AT';

    public static function boot()
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
