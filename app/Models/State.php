<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $table = 'SS_STATE';
    protected $primaryKey 	= 'PK_NO';
    public $timestamps 		= false;

    public function getStateCombo(){
        return State::pluck('STATE_NAME', 'PK_NO');
    }

    public function city() {
        return $this->hasOne('App\Models\City', 'F_STATE_NO', 'PK_NO');
    }

    public function getStateByCountry($id)
    {
         $data = State::where('F_COUNTRY_NO',$id)->get();
        //  $response = '<option value="">Select State</option>';
         $response = null;

            if ($data) {
                foreach ($data as $value) {
                    $response .= '<option value="'.$value->PK_NO.'">'.$value->STATE_NAME.'</option>';
                }
            }else{
                $response .= '<option value="">No data found</option>';
            }
        return $response;
    }

    public function getStateByCity($id)
    {
         $data = City::select('F_STATE_NO','STATE_NAME')->where('PK_NO',$id)->get();
        //  $data2 = City::select('F_STATE_NO','STATE_NAME')->where('CITY_NAME',$data->CITY_NAME)->groupBy('F_STATE_NO')->get();
         $response = '<option value="">Select State</option>';

            if ($data) {
                foreach ($data as $value) {
                    $response .= '<option value="'.$value->F_STATE_NO.'">'.$value->STATE_NAME.'</option>';
                }
            }else{
                $response .= '<option value="">No data found</option>';
            }
        return $response;
    }
}
