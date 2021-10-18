<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class SubCategory extends Model
{
    protected $table        = 'PRD_SUB_CATEGORY';
    protected $primaryKey   = 'PK_NO';
    // public $timestamps      = false;
    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CODE', 'NAME', 'HS_PREFIX'
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

    public function getSubCategoryCombo()
        {
            return SubCategory::pluck('NAME', 'PK_NO');
        }

    public function getSubcateByCategor($id, $type = null)
        {
            $data = SubCategory::where('F_PRD_CATEGORY_NO',$id)->get();
            $response = null;

            if ($type == 'list') {
                $response = [];
                if ($data) {
                    foreach ($data as $key => $value) {
                        $response[$value->PK_NO] = $value->NAME;
                    }
                }

            }else{
                $response .= '<option value="">- Select Subcategory -</option>';
                if ($data) {
                    foreach ($data as $value) {
                        $response .= '<option value="'.$value->PK_NO.'">'.$value->NAME.'</option>';
                    }
                }else{
                    $response .= '<option value="">No data found</option>';
                }
            }

            return $response;
        }

    public function category()
        {
            return $this->belongsTo('App\Models\Category', 'F_PRD_CATEGORY_NO');
        }


}
