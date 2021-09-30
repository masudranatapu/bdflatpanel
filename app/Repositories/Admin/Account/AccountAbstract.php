<?php
namespace App\Repositories\Admin\Account;

use App\Models\AccountSource;
use App\Traits\RepoResponse;
use DB;

class AccountAbstract implements AccountInterface
{
    use RepoResponse;

    protected $account;

    public function __construct(AccountSource $account)
    {
        $this->account = $account;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        $data = $this->account->select('PK_NO','BANK_NAME','BANK_ACC_NO','BANK_ACC_NAME','IS_ACTIVE')->where('IS_ACTIVE',1)->orderBy('BANK_NAME', 'ASC')->get();
        //dd($data);
        return $this->formatResponse(true, '', 'admin.account.list', $data);
    }

    public function postStore($request)
    {
        //dd($request);
        $check_dup = AccountSource::where('BANK_NAME',$request->bank_name)->first();
        if ($check_dup !== null) {
            return $this->formatResponse(false, 'Duplicate entry for Bank Acc Add !', 'admin.account.list');
        }
        DB::beginTransaction();
        try {
            $account                        = new AccountSource();
            $account->BANK_NAME             = $request->bank_name;
            $account->BANK_ACC_NAME         = $request->bank_acc_name;
            $account->BANK_ACC_NO           = $request->bank_acc_no;
            $account->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.account.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Bank Account has been created successfully !', 'admin.account.list');
    }

    public function postUpdate($request, $PK_NO)
    {
        /*$check_dup = AccountSource::where('BANK_NAME',$request->bank_name)->first();
        if ($check_dup !== null) {
            return $this->formatResponse(false, 'Duplicate entry for Bank Account !', 'admin.account.list');
        }*/

        $account = AccountSource::where('PK_NO', $PK_NO)->first();
        $account->BANK_NAME             = $request->bank_name;
        $account->BANK_ACC_NAME         = $request->bank_acc_name;
        $account->BANK_ACC_NO           = $request->bank_acc_no;

        // $accSource->F_PRD_BRAND_NO = $request->brand;

        if ($account->update()) {
            return $this->formatResponse(true, 'Bank Account has been Updated successfully', 'admin.account.list');
        }

        return $this->formatResponse(false, 'Unable to update Bank Account !', 'admin.account.list');
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
}
