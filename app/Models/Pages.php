<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Pages extends Model
{
    protected $table = 'WEB_SEARCH_PAGES';
    protected $primaryKey = 'PK_NO';
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'MODIFIED_AT';

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

    public function pageCategory(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo('App\Models\PagesCategory', 'F_PAGE_CATEGORY_NO', 'PK_NO');
    }
}
