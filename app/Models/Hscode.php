<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hscode extends Model
{
    protected $table        = 'PRD_HS_CODE';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'CODE', 'F_PRD_SUB_CATEGORY_NO'
    ];


    public function getHscodeCombo($scat_id,$type=null){

        $data = Hscode::where('F_PRD_SUB_CATEGORY_NO', $scat_id)->get();
        
        if ($type == 'list') {
            $response = [];
            if ($data) {
                foreach ($data as $key => $value) {
                    $response[$value->CODE] = $value->CODE;
                }
            }
            
        }else{

           $response = '';
           
           if ($data) {
              $response .= '<option value="">- Select HS code -</option>';
              
              foreach ($data as $value) {
                $NARRATION = '';
                if($value->NARRATION){
                    $NARRATION = ' - '. substr($value->NARRATION,0,10);
                }

                   $response .= '<option value="'.$value->CODE.'"  title="'.$value->NARRATION.'">'.$value->CODE.$NARRATION.'</option>';
               }
           }else{
               $response .= '<option value="">No data found</option>';
           } 
 
        }
        
        

        return $response;
    }

   


}
