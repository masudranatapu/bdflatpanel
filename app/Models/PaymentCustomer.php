<?php

namespace App\Models;

use App\Traits\RepoResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

class PaymentCustomer extends Model
{
    use RepoResponse;
    protected $table = 'ACC_CUSTOMER_PAYMENTS';

    protected $primaryKey = 'PK_NO';
    const CREATED_AT = 'SS_CREATED_ON';
    const UPDATED_AT = 'SS_MODIFIED_ON';
    protected $fillable = ['CODE', 'CUSTOMER_NAME'];

    private $user_id;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $user = Auth::user();
            $model->F_SS_CREATED_BY = $user->PK_NO ?? $model->getsetApiAuthId();
        });

        static::updating(function ($model) {
            $user = Auth::user();
            $model->F_SS_MODIFIED_BY = $user->PK_NO ?? $model->getsetApiAuthId();
        });
    }

    public function setApiAuthId($user_id)
    {
        $this->user_id = $user_id;
    }

    public function getsetApiAuthId()
    {
        return $this->user_id;
    }

    public function entryBy()
    {
        return $this->belongsTo('App\Models\Auth', 'F_SS_CREATED_BY');
    }


    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'F_CUSTOMER_NO');
    }

    public function bankTxn()
    {
        return $this->hasOne('App\Models\AccBankTxn', 'F_CUSTOMER_PAYMENT_NO', 'PK_NO');
    }

    public function allOrderPayments()
    {
        return $this->hasMany('App\Models\OrderPayment', 'F_ACC_CUSTOMER_PAYMENT_NO', 'PK_NO');
    }

    public function getRefundRequest($request)
    {
        return DB::table('ACC_CUSTOMER_REFUND')
            ->select('ACC_CUSTOMER_REFUND.*', 'WEB_USER.CODE as USER_CODE', 'WEB_USER.NAME as USER_NAME', 'WEB_USER.MOBILE_NO as USER_MOBILE_NO', 'ACC_CUSTOMER_TRANSACTION.CODE as TID')
            ->leftJoin('WEB_USER', 'WEB_USER.PK_NO', 'ACC_CUSTOMER_REFUND.F_USER_NO')
            ->leftJoin('ACC_CUSTOMER_TRANSACTION', 'ACC_CUSTOMER_TRANSACTION.F_LISTING_LEAD_PAYMENT_NO', 'ACC_CUSTOMER_REFUND.F_LISTING_LEAD_PAYMENT_NO')
            ->paginate(10);
    }

    public function getRefund($id)
    {
        return RefundRequest::query()
            ->select('ACC_CUSTOMER_REFUND.*', 'L.TITLE', 'L.CODE AS PROPERTY_ID', 'U.NAME AS OWNER_NAME', 'LLP.PURCHASE_DATE')
            ->leftJoin('ACC_LISTING_LEAD_PAYMENTS AS LLP', 'LLP.PK_NO', '=', 'ACC_CUSTOMER_REFUND.F_LISTING_LEAD_PAYMENT_NO')
            ->leftJoin('PRD_LISTINGS AS L', 'L.PK_NO', '=', 'LLP.F_LISTING_NO')
            ->leftJoin('WEB_USER AS U', 'U.PK_NO', '=', 'L.F_USER_NO')
            ->find($id);
    }

    public function updateRefund(Request $request, $id): object
    {
        DB::beginTransaction();
        try {
            $refund = $this->getRefund($id);
            if ($refund->STATUS == 2) {
                throw new \Exception('Can not change');
            }

            $refund->STATUS = $request->status;
            $refund->ADMIN_NOTE = $request->note;

            if ($request->status == 2) {
                $refund->APPROVED_AT = date('Y-m-d H:i:s');
                $refund->APPROVED_BY = Auth::id();
            }
            $refund->save();
            DB::table('ACC_LISTING_LEAD_PAYMENTS')->where('PK_NO', $refund->F_LISTING_LEAD_PAYMENT_NO)->delete();

        } catch (\Exception $e) {
            DB::rollBack();
        }

        DB::commit();
        return $this->formatResponse(true, 'Refund request updated', 'admin.refund_request');
    }

    public function getTransactions($date_from = null, $date_to = null, $type = 'all')
    {
        $data = DB::table('ACC_CUSTOMER_TRANSACTION')
            ->select('ACC_CUSTOMER_TRANSACTION.CODE', 'WEB_USER.CODE AS CUSTOMER_NO', 'ACC_CUSTOMER_TRANSACTION.TRANSACTION_DATE', 'ACC_CUSTOMER_TRANSACTION.TRANSACTION_TYPE', 'ACC_CUSTOMER_TRANSACTION.AMOUNT', 'ACC_CUSTOMER_TRANSACTION.IN_OUT')
            ->leftJoin('WEB_USER', 'WEB_USER.PK_NO', 'ACC_CUSTOMER_TRANSACTION.F_CUSTOMER_NO');

        // $transactions = PaymentCustomer::with(['customer' => function ($query) {
        //     $query->select('CODE');
        // }])->take($limit);
        // if ($date_from) {
        //     $transactions->whereDate('PAYMENT_DATE', '>=', date('Y-m-d', strtotime($date_from)));
        // }
        // if ($date_to) {
        //     $transactions->whereDate('PAYMENT_DATE', '<=', date('Y-m-d', strtotime($date_to)));
        // }

        return $data->orderBy('ACC_CUSTOMER_TRANSACTION.PK_NO', 'DESC')->get();
    }

    public function getRechargeRequests($request){
        $data =  DB::table('ACC_RECHARGE_REQUEST')->get();
        return $data;
    }

    public function getRechargeRequest($id)
    {
        return RechargeRequest::query()
            ->select('ACC_RECHARGE_REQUEST.*', 'C.NAME AS C_NAME', 'C.CODE AS C_CODE', 'C.MOBILE_NO AS C_MOBILE_NO', 'C.USER_TYPE')
            ->leftJoin('WEB_USER AS C', 'C.PK_NO', '=', 'ACC_RECHARGE_REQUEST.F_CUSTOMER_NO')
            ->where('ACC_RECHARGE_REQUEST.PK_NO', '=', $id)
            ->first();
    }

    public function updateRechargeRequest($request, $id)
    {

        DB::beginTransaction();
        try {
            $recharge = RechargeRequest::find($id);
            if ($recharge->STATUS == 1) {
                throw new \Exception('Can not change');
            }

            $recharge->STATUS = $request->status;
            $recharge->update();

            if($request->status == 1){   
                      
                DB::table('ACC_CUSTOMER_PAYMENTS')->insert(['F_CUSTOMER_NO' => $recharge->F_CUSTOMER_NO , 'AMOUNT' => $recharge->AMOUNT, 'F_ACC_PAYMENT_BANK_NO' => $recharge->F_PAYMENT_BANK_ACC, 'PAYMENT_NOTE' => $recharge->PAYMENT_NOTE, 'SLIP_NUMBER' => $recharge->SLIP_NUMBER, 'PAYMENT_DATE' => $recharge->PAYMENT_DATE, 'IS_ACTIVE' => 1, 'F_SS_CREATED_BY' => Auth::id(),'SS_CREATED_ON' => date('Y-m-d H:i:s'), 'PAYMENT_TYPE' => 1 ]);

            }

        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse(true, 'Refund request updated', 'admin.recharge_request');
    }


}
