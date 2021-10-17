<?php
namespace App\Repositories\Admin\AccountMethod;

use DB;
use App\Models\AccountMethod;
use App\Traits\RepoResponse;

class AccountMethodAbstract implements AccountMethodInterface
{
    use RepoResponse;

    protected $accountMethod;

    public function __construct(AccountMethod $accountMethod)
    {
        $this->accountMethod = $accountMethod;
    }

    // public function getPaginatedList($request, int $per_page = 10)
    // {
    //     $data = $this->accountMethod->paginate($per_page);
    //     return $this->formatResponse(true, '', 'admin.sub_category.list', $data);
    // }

    public function postStore($request)
    {
        $check_dup = AccountMethod::where('NAME',$request->method)->where('F_ACC_SOURCE_NO', $request->name)->first();
        if ($check_dup !== null) {
            return $this->formatResponse(false, 'Duplicate entry for Payment Method !', 'admin.account.list');
        }
        DB::beginTransaction();

        try {
            $accountMethod                      = new AccountMethod();
            $accountMethod->F_ACC_SOURCE_NO     = $request->name;
            $accountMethod->NAME                = $request->method;
            $accountMethod->IS_ACTIVE           = 1;
            //$sub_category->CODE              = $request->code;
            $accountMethod->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.account.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Payment Method has been created successfully !', 'admin.account.list');
    }

    public function postUpdate($request, $PK_NO)
    {
        $check_dup = AccountMethod::where('NAME',$request->method)->where('F_ACC_SOURCE_NO', $request->name)->first();
        if ($check_dup !== null) {
            return $this->formatResponse(false, 'Duplicate entry for Payment Method !', 'admin.account.list');
        }
        $method_Info = AccountMethod::where('PK_NO', $PK_NO)->first();
        $method_Info->NAME = $request->method;

        // $method_Info->F_PRD_BRAND_NO = $request->brand;

        if ($method_Info->update()) {
            return $this->formatResponse(true, 'Payment Method has been Updated successfully', 'admin.account.list');
        }

        return $this->formatResponse(false, 'Unable to update Payment Method !', 'admin.account.list');
    }

    public function delete($PK_NO)
    {
        $method_Info = AccountMethod::where('PK_NO',$PK_NO)->first();
        $method_Info->IS_ACTIVE = 0;
        if ($method_Info->update()) {
            return $this->formatResponse(true, 'Successfully deleted payment method', 'admin.account.list');
        }
        return $this->formatResponse(false,'Unable to delete payment method','admin.account.list');
    }
}
