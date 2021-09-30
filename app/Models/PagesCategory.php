<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PagesCategory extends Model
{
    protected $table = 'WEB_PAGE_CATEGORY';
    protected $primaryKey = 'PK_NO';
    const CREATED_AT = 'CREATED_ON';
    const UPDATED_AT = 'MODIFIED_ON';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->F_CREATED_BY = Auth::id();
        });

        static::updating(function ($model) {
            $model->F_MODIFIED_BY = Auth::id();
        });
    }
}
