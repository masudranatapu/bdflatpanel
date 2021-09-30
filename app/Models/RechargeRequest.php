<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RechargeRequest extends Model
{
    protected $table = 'ACC_RECHARGE_REQUEST';
    protected $primaryKey = 'PK_NO';
    const CREATED_AT = 'SS_CREATED_ON';
    const UPDATED_AT = 'SS_MODIFIED_ON';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->F_SS_CREATED_BY = \Illuminate\Support\Facades\Auth::id();
        });

        static::updating(function ($model) {
            $model->F_SS_MODIFIED_BY = \Illuminate\Support\Facades\Auth::id();
        });
    }
}
