<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class BookingDetails extends Model
{
    protected $table 		= 'SLS_BOOKING_DETAILS';
    protected $primaryKey   = 'PK_NO';
    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';

    protected $fillable = [ 'F_BOOKING_NO'];

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

    public function booking() {
        return $this->belongsTo('App\Models\Booking', 'F_BOOKING_NO', 'PK_NO');
    }

    public function consignment() {
        return $this->hasOne('App\Models\DispatchDetails', 'F_BOOKING_DETAILS_NO', 'PK_NO');
    }

    public function bundle() {
        return $this->belongsTo('App\Models\Offer', 'F_BUNDLE_NO', 'PK_NO');
    }

    public function stock() {
        return $this->belongsTo('App\Models\Stock', 'F_INV_STOCK_NO', 'PK_NO');
    }


    public function deliveryAddress() {
        return $this->belongsTo('App\Models\CustomerAddress','CURRENT_F_DELIVERY_ADDRESS', 'PK_NO');
    }





    public function getAgentCombo(){
        return Agent::where('IS_ACTIVE', 1)->pluck('NAME', 'PK_NO');
    }

    public function getAgentComboCustomer(Type $var = null)
    {
        $response = '';
        $data = Agent::select('NAME','PK_NO')->where('IS_ACTIVE', 1)->get();

        if ($data && count($data) > 0) {
            foreach ($data as $value) {
                $response .= '<option value="'.$value->PK_NO.'">'.$value->NAME.'</option>';
            }
        }else{
            $response .= '<option value="">No data found</option>';
        }
        return $response;
    }

    public function reseller() {
        return $this->hasMany('App\Models\Reseller', 'F_AGENT_NO', 'PK_NO');
    }

    public function customer() {
        return $this->hasMany('App\Models\Customer', 'F_SALES_AGENT_NO', 'PK_NO');
    }
}
