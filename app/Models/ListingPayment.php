<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ListingPayment extends Model
{
    protected $table = 'ACC_LISTING_PAYMENTS';
    protected $primaryKey = 'PK_NO';
    const CREATED_AT = 'CREATE_AT';
    const UPDATED_AT = 'MODIFIED_AT';
    protected $fillable = ['F_LISTING_NO', 'F_USER_NO', 'AMOUNT'];

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
