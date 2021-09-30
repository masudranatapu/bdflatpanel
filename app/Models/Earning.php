<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Earning extends Model
{
    protected $table 		= 'SLS_AGENTS';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    protected $fillable = [
        'NAME'
    ];

    public function getAgentCombo(){
        return Agent::where('IS_ACTIVE', 1)->pluck('NAME', 'PK_NO');
    }

    public function getAgentComboCustomer(Type $var = null)
    {
        $response = '';
        $data = Agent::select('NAME','PK_NO')->where('IS_ACTIVE', 1)->get();

        if ($data) {
            foreach ($data as $value) {
                $response .= '<option value="'.$value->PK_NO.'">'.$value->NAME.'</option>';
            }
        }else{
            $response .= '<option value="">No data found</option>';
        }
        return $response;
    }

    public function getUKCombo(Type $var = null)
    {
        return (object) array('0' => 'AZURAMART');
    }

    public function getUKComboCustomer(Type $var = null)
    {
        return '<option value="0">AZURAMART</option>';
    }

    public function reseller() {
        return $this->hasMany('App\Models\Reseller', 'F_AGENT_NO', 'PK_NO');
    }

    public function customer() {
        return $this->hasMany('App\Models\Customer', 'F_SALES_AGENT_NO', 'PK_NO');
    }
}
