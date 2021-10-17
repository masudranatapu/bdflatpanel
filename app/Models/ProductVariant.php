<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;

class ProductVariant extends Model
{
    protected $table        = 'PRD_VARIANT_SETUP';
    protected $primaryKey   = 'PK_NO';
    const CREATED_AT     = 'SS_CREATED_ON';
    const UPDATED_AT     = 'SS_MODIFIED_ON';


    protected $fillable = [
        'CODE', 'COMPOSITE_CODE', 'VARIANT_NAME', 'COLOR', 'HS_CODE', 'SIZE_NAME', 'COLOR'
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



    public function allVariantPhotos()
    {
        return $this->hasMany('App\Models\ProdImgLib', 'F_PRD_VARIANT_NO');
    }

    public function master()
    {
        return $this->belongsTo('App\Models\Product', 'F_PRD_MASTER_SETUP_NO');
    }

    public function vatclass()
    {
        return $this->belongsTo('App\Models\VatClass', 'F_VAT_CLASS')->where('IS_ACTIVE',1)->orderBy('NAME','ASC');
    }


    public function getProductVariantInfo(array $variant_pk_nos)
    {
        return ProductVariant::whereIn('PK_NO',$variant_pk_nos)->get();
        // return $variant_pk_nos;
    }











}
