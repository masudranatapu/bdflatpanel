<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;
class ProductModel extends Model
{
    protected $table 		= 'PRD_MODEL';
    protected $primaryKey   = 'PK_NO';
	// public $timestamps 		= false;
    const CREATED_AT     = 'SS_CREATED_ON';
    const UPDATED_AT     = 'SS_MODIFIED_ON';


    protected $fillable = ['NAME'];

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


    public function brand() {
        return $this->belongsTo('App\Models\Brand', 'F_PRD_BRAND_NO');
    }



    public function getProdModel($brand_id,$type = null){

    	$data = ProductModel::where('F_PRD_BRAND_NO',$brand_id)->get();
        $response = null;

        if ($type == 'list') {
            $response = [];
            if ($data) {
                foreach ($data as $key => $value) {
                    $response[$value->PK_NO] = $value->NAME;
                }
            }

        }else{
            $response = '<option data-igprifix="" value=""> - select product model - </option>';
            if ($data) {
                foreach ($data as $value) {
                    $response .= '<option data-igprifix="'.$value->COMPOSITE_CODE.'" value="'.$value->PK_NO.'">'.$value->NAME.'</option>';
                }
            }else{
                $response .= '<option data-igprifix="" value="">No data found</option>';
            }
        }


        return $response;
    }


}
