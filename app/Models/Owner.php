<?php

namespace App\Models;

use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $table = 'WEB_USER';
    protected $primaryKey = 'PK_NO';
    const CREATED_AT = 'CREATED_AT';
    const UPDATED_AT = 'UPDATED_AT';

    protected $fillable = ['NAME'];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->CREATED_BY = Auth::id();
        });

        static::updating(function ($model) {
            $model->UPDATED_BY = Auth::id();
        });
    }

    public function properties(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany('App\Models\Product', 'F_USER_NO', 'PK_NO');
    }

    public function info(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne('App\Models\OwnerInfo', 'F_USER_NO', 'PK_NO');
    }

    /*

     public function getResellerCombo(){
         return Owner::where('IS_ACTIVE', 1)->pluck('NAME', 'PK_NO');
     }

     public function getResellerComboCustomer(Type $var = null)
     {
         $response = '';
         $data = Owner::select('NAME','PK_NO')->where('IS_ACTIVE', 1)->get();
         if ($data) {
             foreach ($data as $value) {
                 $response .= '<option value="'.$value->PK_NO.'">'.$value->NAME.'</option>';
             }
         }else{
             $response .= '<option value="">No data found</option>';
         }
         return $response;
     }
     public function customer() {
         return $this->hasMany('App\Models\Customer', 'F_RESELLER_NO', 'PK_NO');
     }

     public function agent() {
         return $this->hasOne('App\Models\Agent','PK_NO', 'F_PREFERRED_AGENT_NO');
     }

     public function state() {
         return $this->hasOne('App\Models\State', 'PK_NO', 'STATE');
     }

     public function city() {
         return $this->hasOne('App\Models\City', 'PK_NO', 'CITY');
     }

     public function country() {
         return $this->hasOne('App\Models\Country', 'PK_NO', 'F_COUNTRY_NO');
     }
     */


}
