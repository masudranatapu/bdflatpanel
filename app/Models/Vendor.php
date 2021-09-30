<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Vendor extends Model
{
    protected $table = 'PRC_VENDORS';
    protected $primaryKey 	= 'PK_NO';
    const CREATED_AT     = 'SS_CREATED_ON';
    const UPDATED_AT     = 'SS_MODIFIED_ON';

    protected $fillable 	= ['PK_NO','CODE'];


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

	public function getVendorCombo(){
        return Vendor::pluck('NAME', 'PK_NO');

    }
}
