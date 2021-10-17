<?php

namespace App\Models;

use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table 		= 'WEB_USER';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;
    /*const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';*/

    protected $fillable = ['NAME'];

    public function getCustomerCombo(){
        return Customer::where('IS_ACTIVE', 1)->pluck('NAME', 'PK_NO');
    }

    public function getCustomerCombo20(){
        return Customer::where('IS_ACTIVE', 1)->pluck('NAME', 'PK_NO')->take(5);
    }

    public function address() {
        return $this->hasMany('App\Models\CustomerAddress', 'F_CUSTOMER_NO', 'PK_NO');
    }

    public function agent() {
        return $this->belongsTo('App\Models\Agent', 'F_SALES_AGENT_NO', 'PK_NO')->orderBy('NAME','ASC');
    }

    public function reseller() {
        return $this->belongsTo('App\Models\Reseller', 'F_RESELLER_NO', 'PK_NO');
    }

    public function country() {
        return $this->hasOne('App\Models\Country', 'PK_NO', 'F_COUNTRY_NO');
    }

    public function propertyRequirement()
    {
        return $this->hasOne('App\Models\ProductRequirements', 'F_USER_NO', 'PK_NO')->where('IS_ACTIVE', '=', 1);
    }

    /*public static function boot()
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
   }*/




}
