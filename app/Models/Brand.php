<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Brand extends Model
{

    protected $table        = 'PRD_BRAND';
    protected $primaryKey   = 'PK_NO';
    // public $timestamps      = false;
    const CREATED_AT     = 'SS_CREATED_ON';
    const UPDATED_AT     = 'SS_MODIFIED_ON';

    protected $fillable = [ 'NAME'];

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




    public function getBrandCombo()
    {
      return Brand::where('IS_ACTIVE',1)->pluck('NAME', 'PK_NO');
    }


    public function productModel() {
        return $this->hasMany('App\Models\ProductModel', 'F_PRD_BRAND_NO')->where('IS_ACTIVE',1)->orderBy('NAME','ASC');
    }

    public function productColor() {
        return $this->hasMany('App\Models\Color', 'F_BRAND')->where('IS_ACTIVE',1)->orderBy('NAME','ASC');
    }
    public function productSize() {
        return $this->hasMany('App\Models\ProductSize', 'F_BRAND_NO')->where('IS_ACTIVE',1)->orderBy('NAME','ASC');
    }







}
