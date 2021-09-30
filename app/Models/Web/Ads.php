<?php

namespace App\Models\Web;

use Illuminate\Support\Str;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;

class Ads extends Model
{
    use RepoResponse;

    protected $table = 'PRD_ADS';
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

    public function position(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\Models\Web\AdsPosition', 'POSITION_ID', 'F_AD_POSITION_NO');
    }

    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\Web\AdsImages', 'F_ADS_NO', 'PK_NO')->orderByDesc('ORDER_ID');
    }
}
