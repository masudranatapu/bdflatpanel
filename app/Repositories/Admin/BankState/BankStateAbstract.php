<?php
namespace App\Repositories\Admin\BankState;

use DB;
use Auth;
use Importer;
use App\Models\AccBankTxn;
use App\Traits\RepoResponse;
use App\Models\BankStatement;

class BankStateAbstract implements BankStateInterface
{
    use RepoResponse;
    protected $statement;

    public function __construct(BankStatement $statement)
    {
        $this->statement = $statement;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        if($request->status == 'draft'){
            $data = $this->statement->where('IS_DRAFT',1)->where('MARK_AS_USED',0)->orderBy('TXN_DATE', 'DESC')->get();
        }elseif($request->status == 'used'){
            $data = $this->statement->where('IS_DRAFT',0)
                    ->where('MARK_AS_USED',1)
                    ->orWhere('IS_MATCHED',1)
                    ->orderBy('TXN_DATE', 'DESC')->get();
        }else{
            $data = $this->statement->where('IS_DRAFT',0)
            ->where('IS_MATCHED',0)
            ->where('MARK_AS_USED',0)
            ->orderBy('TXN_DATE', 'DESC')
            ->get();

        }
        return $this->formatResponse(true, '', 'admin.account.list', $data);
    }


    public function postStore($request)
    {
        DB::beginTransaction();

        try {

            $importer = Importer::make('Csv');
            $filepath = $request->file('statement_file')->getRealPath();
            $importer->load($filepath);
            $collection = $importer->getCollection();
                if($collection){
                    foreach ($collection as $key => $value) {
                        if($key > 0 ){
                            $debit      = 0;
                            $credit     = 0;
                            $tran_date  = date('Y-m-d', strtotime($value[0]));

                            if($value[2] != ''){
                                $debit = filter_var($value[2], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                            }

                            if($value[3] != ''){
                                $credit = filter_var($value[3], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                            }

                            $insert_data[] = array(
                                'TXN_DATE'                  => $tran_date,
                                'NARRATION'                 => $value[1],
                                'DR_AMOUNT'                 => $debit,
                                'CR_AMOUNT'                 => $credit,
                                'SS_CREATED_ON'             => date('Y-m-d H:s:i'),
                                'F_SS_CREATED_BY'           => Auth::user()->PK_NO,
                                'F_ACC_BANK_PAYMENT_NO'     => $request->payment_acc_no,
                                'IS_MATCHED'                => 0,
                                'IS_DRAFT'                  => 1,
                                //'IS_VARIFIED'               => 0,
                            );
                        }

                    }

                    if(!empty($insert_data)){

                        BankStatement::insert($insert_data);
                    }

                }

            } catch (\Exception $e) {

                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.bankstate.list');
        }
                DB::commit();

            return $this->formatResponse(true, 'Bank statement uploaded successfully !', 'admin.bankstate.list');

    }

    public function postDraftToSave($request)
    {
        DB::beginTransaction();
        try {
            $draft_decords_array = $request->draft;
            BankStatement::whereIn('PK_NO', $draft_decords_array)->update(['IS_DRAFT' => 0]);
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.bankstate.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Bank statements save from draft successfully !', 'admin.bankstate.list');


    }

    public function delete($PK_NO)
    {   DB::beginTransaction();
        try {
                BankStatement::where('PK_NO',$PK_NO)->where('IS_MATCHED',0)->delete();
            } catch (\Exception $e) {
                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.bankstate.list');
            }
                DB::commit();
                return $this->formatResponse(true, 'Bank statement deleted successfully !', 'admin.bankstate.list');
    }

    public function postDeleteBulk($request)
    {
        DB::beginTransaction();
        try {
                BankStatement::whereIn('PK_NO',$request->pk_no)->where('IS_MATCHED',0)->delete();
            } catch (\Exception $e) {
                DB::rollback();
                return $this->formatResponse(false, $e->getMessage(), 'admin.bankstate.list');
            }
                DB::commit();
                return $this->formatResponse(true, 'Bank statement deleted successfully !', 'admin.bankstate.list');
    }


    public function postMarkAsUsed($request)
    {   DB::beginTransaction();
        try {
            $draft_decords_array = $request->draft;
            BankStatement::whereIn('PK_NO', $draft_decords_array)->update(['MARK_AS_USED' => 1]);

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.bankstate.list');
        }
            DB::commit();

        return $this->formatResponse(true, 'Bank statements save from draft successfully !', 'admin.bankstate.list');


    }

    public function postVerify($request)
    {
        DB::beginTransaction();
        try {
            $bs_pk_bo   = $request->bs_pk_no;
            $cp_pk_bo   = $request->cp_pk_no;
            $payment    = AccBankTxn::where('PK_NO',$cp_pk_bo)->where('IS_MATCHED', 0)->first();
            $bank_state  = BankStatement::where('PK_NO',$bs_pk_bo)->where('IS_MATCHED', 0)->first();
            if ($payment != null && $bank_state != null ) {
               DB::statement('CALL PROC_CUSTOMER_PAYMENT_VERIFY(:cp_pk_bo, :bs_pk_bo );',array($cp_pk_bo,$bs_pk_bo));
                $msg = 1;
            }else{
                $msg = 2;
            }

        } catch (\Exception $e) {
        DB::rollback();
            $msg = 0;
            return $this->formatResponse(false, $msg, 'admin.bankstate.verification');
        }
        DB::commit();
        return $this->formatResponse(true, $msg, 'admin.bankstate.verification');
    }


    public function getUnVerify($id)
    {
        DB::beginTransaction();
        try {
            $bank_state  = BankStatement::where('PK_NO',$id)->where('IS_MATCHED', 1)->first();
            if($bank_state){
                DB::statement('CALL PROC_CUSTOMER_PAYMENT_UNVERIFY(:bs_pk_bo );',array($id));
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.bankstate.verification');
        }
        DB::commit();
        return $this->formatResponse(true, 'Bank statements Unverified successfully !', 'admin.bankstate.verification');
    }






}


