<?php

namespace App\Models;
use Auth;
use App\Models\AccBankTxn;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PaymentIxfer extends Model
{
    protected $table 		= 'ACC_PAYMENT_BANK_ACC_IXFER';
	protected $primaryKey 	= 'PK_NO';
    const CREATED_AT     	= 'SS_CREATED_ON';
    const UPDATED_AT     	= 'SS_MODIFIED_ON';

    protected $fillable 	= ['PK_NO','CODE'];

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            $user = Auth::user();
            $model->SS_CREATED_BY = $user->PK_NO;
        });

        static::updating(function($model)
        {
            $user = Auth::user();
            $model->F_SS_MODIFIED_BY = $user->PK_NO;
        });
    }

    public function entryBy()
    {
        return $this->belongsTo('App\Models\Auth', 'SS_CREATED_BY');
    }

    public function from_ix_account()
    {
        return $this->belongsTo('App\Models\PaymentBank', 'F_FROM_ACC_PAYMENT_BANK_ACC_NO');
    }

    public function to_ix_account()
    {
        return $this->belongsTo('App\Models\PaymentBank', 'F_TO_ACC_PAYMENT_BANK_ACC_NO');
    }

    public function approveBy()
    {
        return $this->belongsTo('App\Models\Auth', 'F_VERIFIED_BY_SA_USER_NO');
    }

    public function from_tnx()
    {
        return $this->belongsTo('App\Models\AccBankTxn', 'F_ACC_BANK_TXN');
    }

    public function to_tnx($id)
    {
        return AccBankTxn::where('PK_NO', '>', $id)->orderby('PK_NO','ASC')->first();
    }
}
