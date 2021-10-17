<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
class PropertyCategory extends Model
{
    protected $table        = 'PRD_PROPERTY_TYPE';
    protected $primaryKey   = 'PK_NO';
    const CREATED_AT        = 'CREATED_AT';
    const UPDATED_AT        = 'MODIFIED_AT';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['PROPERTY_TYPE'];

    public static function boot()
        {
           parent::boot();
           static::creating(function($model)
           {
               $user = Auth::user();
               $model->CREATED_BY = $user->PK_NO;
           });

           static::updating(function($model)
           {
               $user = Auth::user();
               $model->MODIFIED_BY = $user->PK_NO;
           });
       }


}
