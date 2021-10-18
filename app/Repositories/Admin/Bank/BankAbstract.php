<?php
namespace App\Repositories\Admin\Bank;

use DB;
use App\Models\BankAccount;
use App\Traits\RepoResponse;

class BankAbstract implements BankInterface
{
    use RepoResponse;

    protected $bank_account;

    public function __construct(BankAccount $bank_account)
    {
        $this->bank_account = $bank_account;
    }

    public function getPaginatedList($request, int $per_page = 10)
    {
        $data = $this->bank_account->get();
        return $this->formatResponse(true, '', 'admin.sub_category.list', $data);
    }

    // public function postStore($request)
    // {
    //     echo '<pre>';
    //     echo '======================<br>';
    //     print_r($request->all());
    //     echo '<br>======================';
    //     exit();
    //     DB::beginTransaction();

    //     try {
    //         $bank_account                      = new BankAccount();
    //         $bank_account->F_ACCOUNT_SOURCE_NO = $request->bank_name;
    //         $bank_account->NAME                = $request->name;
    //         //$sub_category->CODE              = $request->code;
    //         $bank_account->save();

    //     } catch (\Exception $e) {

    //         DB::rollback();
    //         return $this->formatResponse(false, $e->getMessage(), 'admin.sub_category.list');
    //     }
    //     DB::commit();

    //     return $this->formatResponse(true, 'Account name has been created successfully !', 'admin.sub_category.list');
    // }

    public function postStoreSingle($request)
    {
        $check_dup = BankAccount::where('NAME',$request->bank_name)->where('F_ACCOUNT_SOURCE_NO', $request->name)->first();
        if ($check_dup !== null) {
            return $this->formatResponse(false, 'Duplicate entry for Account Name !', 'admin.account.list');
        }
        DB::beginTransaction();

        try {
            $bank_account                      = new BankAccount();
            $bank_account->F_ACCOUNT_SOURCE_NO = $request->name;
            $bank_account->NAME                = $request->bank_name;
            $bank_account->IS_ACTIVE           = 1;
            //$sub_category->CODE              = $request->code;
            $bank_account->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.account.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Account Name has been created successfully !', 'admin.account.list');
    }

    public function postUpdate($request, $PK_NO)
    {
        $check_dup = BankAccount::where('NAME',$request->bank_name)->where('F_ACCOUNT_SOURCE_NO', $request->name)->first();
        if ($check_dup !== null) {
            return $this->formatResponse(false, 'Duplicate entry for Account Name !', 'admin.account.list');
        }
        $bankInfo = BankAccount::where('PK_NO', $PK_NO)->first();
        $bankInfo->NAME = $request->bank_name;

        // $bankInfo->F_PRD_BRAND_NO = $request->brand;

        if ($bankInfo->update()) {
            return $this->formatResponse(true, 'Account Name has been Updated successfully', 'admin.account.list');
        }

        return $this->formatResponse(false, 'Unable to update Account Name !', 'admin.account.list');
    }

    public function delete($PK_NO)
    {
        $bankInfo = BankAccount::where('PK_NO',$PK_NO)->first();
        $bankInfo->IS_ACTIVE = 0;
        if ($bankInfo->update()) {
            return $this->formatResponse(true, 'Successfully deleted Account Name', 'admin.account.list');
        }
        return $this->formatResponse(false,'Unable to delete Account Name','admin.account.list');
    }
}
