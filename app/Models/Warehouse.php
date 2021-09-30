<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Auth;

class Warehouse extends Model
{
    protected $table = 'INV_WAREHOUSE';

    public $timestamps 		= false;
    protected $primaryKey 	= 'PK_NO';
    protected $fillable 	= ['PK_NO','CODE'];

	// const CREATED_AT = 'create_dttm';
	// const UPDATED_AT = 'update_dttm';

	public function getWarehpuseCombo()
	{
        return Warehouse::pluck('NAME', 'PK_NO');
    }

    public static function boot()
    {
       parent::boot();
       static::creating(function($model)
       {
           $user 						= Auth::user();
           $model->F_PROCESS_RUN_BY 	= $user->PK_NO;
           $model->PROCESS_RUN_BY 		= $user->USERNAME;
       });

       // static::updating(function($model)
       // {
       //     $user = Auth::user();
       //     $model->F_SS_MODIFIED_BY = $user->id;
       // });

   	}


}
