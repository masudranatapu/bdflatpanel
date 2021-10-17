<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Color extends Model
{

    protected $table 		= 'PRD_COLOR';
    protected $primaryKey   = 'PK_NO';
    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';
    protected $fillable     = ['CODE', 'NAME'];


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


    public function getColorCombo($brand_id){
        $color = Color::where('F_BRAND',$brand_id)->get();

        $response = [];
        if ($color) {
            foreach ($color as $key => $value) {

                $response[$value->PK_NO] = $value->NAME;
            }
        }
        return $response;
    }


    public function brand() {
        return $this->belongsTo('App\Models\Brand', 'F_BRAND');
    }





}
