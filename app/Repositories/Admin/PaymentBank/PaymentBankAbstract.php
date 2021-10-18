<?php

namespace App\Repositories\Admin\PaymentBank;

use App\Models\PaymentBankAcc;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;

class PaymentBankAbstract implements PaymentBankInterface
{
    use RepoResponse;

    protected $account;

    public function __construct(PaymentBankAcc $account)
    {
        $this->account = $account;
    }

    public function getPaginatedList($request, int $per_page = 5): object
    {
        $data = $this->account->orderBy('BANK_NAME', 'ASC')->get();
        //dd($data);
        return $this->formatResponse(true, '', 'admin.payment_bank.list', $data);
    }

    public function postStore($request): object
    {
        DB::beginTransaction();
        try {
            $code = PaymentBankAcc::max('CODE');
            if (!$code) {
                $code = 100;
            }
            $code++;
            $account = new PaymentBankAcc();
            $account->CODE = $code;
            $account->BANK_NAME = $request->bank_name;
            $account->BANK_ACC_NAME = $request->bank_acc_name;
            $account->BANK_ACC_NO = $request->bank_acc_no;
            $account->IS_ACTIVE = $request->status;
            $account->COMMENTS = $request->comment;
            $account->F_PAYMENT_METHOD_NO = $request->payment_method;
            $account->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.payment_acc.list');
        }

        DB::commit();
        return $this->formatResponse(true, 'Payment account has been created successfully !', 'admin.payment_acc.list');
    }

    public function postUpdate($request, $id)
    {
        DB::beginTransaction();
        try {
            $account = PaymentBankAcc::find($id);
            $account->BANK_NAME = $request->bank_name;
            $account->BANK_ACC_NAME = $request->bank_acc_name;
            $account->BANK_ACC_NO = $request->bank_acc_no;
            $account->IS_ACTIVE = $request->status;
            $account->COMMENTS = $request->comment;
            $account->F_PAYMENT_METHOD_NO = $request->payment_method;
            $account->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.payment_acc.list');
        }

        DB::commit();
        return $this->formatResponse(true, 'Payment account has been updated successfully !', 'admin.payment_acc.list');
    }

    /* public function postUpdate($request, $PK_NO)
     {
         $check_dup = AccountSource::where('NAME',$request->name)->first();
         if ($check_dup !== null) {
             return $this->formatResponse(false, 'Duplicate entry for Payment Source !', 'admin.account.list');
         }

         $accSource = AccountSource::where('PK_NO', $PK_NO)->first();
         $accSource->NAME = $request->name;

         // $accSource->F_PRD_BRAND_NO = $request->brand;

         if ($accSource->update()) {
             return $this->formatResponse(true, 'Payment Source has been Updated successfully', 'admin.account.list');
         }

         return $this->formatResponse(false, 'Unable to update Payment Source !', 'admin.account.list');
     }

     public function delete($PK_NO)
     {
         $accSource = AccountSource::where('PK_NO',$PK_NO)->first();
         $accSource->IS_ACTIVE = 0;
         if ($accSource->update()) {
             return $this->formatResponse(true, 'Successfully deleted Payment Source', 'admin.account.list');
         }
         return $this->formatResponse(false,'Unable to delete Payment Source','admin.account.list');
     }
     */
}
