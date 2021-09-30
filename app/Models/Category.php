<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
class Category extends Model
{
    protected $table        = 'PRD_CATEGORY';
    protected $primaryKey   = 'PK_NO';
    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CODE', 'NAME'
    ];

    public static function boot()
        {
           parent::boot();
           static::creating(function($model)
           {
               $user = Auth::user();
               $model->F_SS_CREATED_BY = $user->PK_NO;
           });

           static::updating(function($model)
           {
               $user = Auth::user();
               $model->F_SS_MODIFIED_BY = $user->PK_NO;
           });
       }

    public function getCategorCombo()
    {
         return Category::where('IS_ACTIVE',1)->pluck('NAME', 'PK_NO');
    }

    public function subcategory()
        {
            return $this->hasMany('App\Models\SubCategory', 'F_PRD_CATEGORY_NO')->where('IS_ACTIVE',1)->orderBy('NAME','ASC');
        }

}
