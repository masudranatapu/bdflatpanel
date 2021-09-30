<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoCode extends Model
{
    protected $table = 'SS_PO_CODE';
    protected $primaryKey 	= 'PK_NO';
    public $timestamps 		= false;

    public function getPoCombo(){
        return PoCode::pluck('PO_CODE', 'PK_NO');
    }

    public function getPcodeByCity($city_id,$state_id)
    {
         $data = PoCode::where('F_STATE_NO',$state_id)->where('F_CITY_NO',$city_id)->get();
         $response = null;

            if ($data) {
                foreach ($data as $value) {
                    $response .= '<option value="'.$value->PK_NO.'">'.$value->PO_CODE.'</option>';
                }
            }else{
                $response .= '<option value="">No data found</option>';
            }


        return $response;
    }

    public function getPostagebyCity($city_id)
    {
         $data = PoCode::where('F_CITY_NO',$city_id)->first();
        return $data;
    }
}
