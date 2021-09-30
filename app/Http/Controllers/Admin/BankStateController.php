<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Models\AccBankTxn;
use Illuminate\Http\Request;
use App\Models\BankStatement;
use App\Models\PaymentBankAcc;
use Illuminate\Support\Collection;
use App\Http\Controllers\BaseController;
use App\Repositories\Admin\BankState\BankStateInterface;


class BankStateController extends BaseController
{
    protected $bankstate ;
    public function __construct(BankStateInterface $bankstate)
    {
        $this->bankstate         = $bankstate;

    }


    public function getIndex(Request $request)
    {
        $this->resp = $this->bankstate->getPaginatedList($request, 50);
        $data['payment_acc_no'] = PaymentBankAcc::where('IS_ACTIVE','1')->get();
        $data['rows'] = $this->resp->data;
        return view('admin.bankstate.index',compact('data'));
    }

    public function getMatchingList(Request $request)
    {
        $acc_bank_txn_no = $request->acc_bank_txn_no;
        $payment  = DB::table('ACC_BANK_TXN')
        ->select('ACC_BANK_TXN.TXN_DATE','ACC_BANK_TXN.AMOUNT_BUFFER','ACC_BANK_TXN.F_ACC_PAYMENT_BANK_NO','ACC_BANK_TXN.F_CUSTOMER_PAYMENT_NO','ACC_BANK_TXN.F_RESELLER_PAYMENT_NO','ACC_BANK_TXN.PAYMENT_TYPE','ACC_CUSTOMER_PAYMENTS.CUSTOMER_NAME','ACC_RESELLER_PAYMENTS.RESELLER_NAME','ACC_CUSTOMER_PAYMENTS.PAID_BY as CU_PAID_BY','ACC_RESELLER_PAYMENTS.PAID_BY as RE_PAID_BY')
        ->leftJoin('ACC_CUSTOMER_PAYMENTS','ACC_CUSTOMER_PAYMENTS.PK_NO','ACC_BANK_TXN.F_CUSTOMER_PAYMENT_NO')
        ->leftJoin('ACC_RESELLER_PAYMENTS','ACC_RESELLER_PAYMENTS.PK_NO','ACC_BANK_TXN.F_RESELLER_PAYMENT_NO')
        ->where('ACC_BANK_TXN.PK_NO', $acc_bank_txn_no)
        ->first();

        $pay_date       = $payment->TXN_DATE;
        $cust_name      = $payment->CUSTOMER_NAME ?? $payment->RESELLER_NAME;
        $paid_by        = $payment->CU_PAID_BY ?? $payment->RE_PAID_BY;
        $pay_amount     = abs($payment->AMOUNT_BUFFER);
        $bank_id        = $payment->F_ACC_PAYMENT_BANK_NO;
        $name_search    = addslashes($paid_by) .' '. addslashes($cust_name);

        $search_param['pay_date'] = $pay_date;
        $search_param['name_search'] = trim($name_search);
        $search_param['cust_name'] = $cust_name;
        $search_param['paid_by'] = $paid_by;
        $search_param['pay_amount'] = $pay_amount;
        $search_param['bank_id'] = $bank_id;
// dd($payment);
        //$res = BankStatement::get();
        if($payment->PAYMENT_TYPE == 2){
            $sqlScript = "SELECT un.PK_NO, un.TXN_DATE, un.F_ACC_BANK_PAYMENT_NO, un.CR_AMOUNT,un.DR_AMOUNT, un.PK_NO, un.NARRATION, MATCH (un.NARRATION) AGAINST ('$name_search') AS title_relevance, bc.BANK_NAME,bc.BANK_ACC_NAME,bc.BANK_ACC_NAME, bc.BANK_ACC_NO FROM ACC_BANK_TXN_STATEMENT un LEFT JOIN ACC_PAYMENT_BANK_ACC ps ON ps.PK_NO = un.F_ACC_BANK_PAYMENT_NO LEFT JOIN ACC_PAYMENT_BANK_ACC bc ON bc.PK_NO = un.F_ACC_BANK_PAYMENT_NO WHERE un.IS_MATCHED = 0 AND un.IS_DRAFT = 0 AND un.DR_AMOUNT = $pay_amount AND un.F_ACC_BANK_PAYMENT_NO = $bank_id  ORDER BY title_relevance DESC, un.TXN_DATE DESC LIMIT 50";
        }else{
            $sqlScript = "SELECT un.PK_NO, un.TXN_DATE, un.F_ACC_BANK_PAYMENT_NO, un.CR_AMOUNT,un.DR_AMOUNT, un.PK_NO, un.NARRATION, MATCH (un.NARRATION) AGAINST ('$name_search') AS title_relevance, bc.BANK_NAME,bc.BANK_ACC_NAME,bc.BANK_ACC_NAME, bc.BANK_ACC_NO FROM ACC_BANK_TXN_STATEMENT un LEFT JOIN ACC_PAYMENT_BANK_ACC ps ON ps.PK_NO = un.F_ACC_BANK_PAYMENT_NO LEFT JOIN ACC_PAYMENT_BANK_ACC bc ON bc.PK_NO = un.F_ACC_BANK_PAYMENT_NO WHERE un.IS_MATCHED = 0 AND un.IS_DRAFT = 0 AND un.CR_AMOUNT = $pay_amount AND un.F_ACC_BANK_PAYMENT_NO = $bank_id  ORDER BY title_relevance DESC, un.TXN_DATE DESC LIMIT 50";
        }

        // return $sqlScript;

        // SELECT un.transaction_date, un.pay_source_pk_no, un.credit, un.bs_pk_no, un.description, ps.pay_source_name, MATCH (un.description) AGAINST ('$name_search') AS title_relevance FROM t_bankstatement_unused un LEFT JOIN s_paysource ps ON ps.pay_source_pk_no = un.pay_source_pk_no WHERE un.status = 0 AND un.varified_flag = 0 AND un.credit = $pay_amount AND un.pay_source_pk_no = $bank_id AND (un.transaction_date = '$pay_date' OR MATCH(un.description) AGAINST('$name_search')) ORDER BY title_relevance DESC, un.transaction_date DESC LIMIT 10

        $result = DB::SELECT($sqlScript);

        foreach ($result as $key => $value) {
            $order = 0 ;
            if ($search_param['pay_date'] == $value->TXN_DATE) {
                $order += 2;
            }
            if ($search_param['bank_id'] == $value->F_ACC_BANK_PAYMENT_NO) {
                $order += 1;
            }
            if($payment->PAYMENT_TYPE == 2){
                if ($search_param['pay_amount'] == $value->DR_AMOUNT) {
                    $order += 3;
                }
            }else{
                if ($search_param['pay_amount'] == $value->CR_AMOUNT) {
                    $order += 3;
                }
            }


            if ($search_param['name_search']) {
                // $c_pb_arr = explode(' ', $search_param['name_search']);
                $c_pb_arr   = preg_split('/\s+/', $search_param['name_search']);
                $des = $this->RemoveSpecialChar(strtolower($value->NARRATION));


                foreach ($c_pb_arr as $key => $pb) {
                    $pb = trim($pb) ? strtolower($pb) : null;
                    if($pb != null){
                        if (strpos($des, $pb) !== false){
                            $order += 1;
                        }
                    }

                }
            }
            $value->row_order = $order;
        }


        $collection = new Collection($result);
        $sorted = $collection->sortByDesc('row_order');
        $res = $sorted->values()->all();

        $returnHTML = view('admin.bankstate._bankstate')->with('rows', $res)->with('search_param', $search_param)->render();
        $arr = array('msg' => 'Bank statement', 'status' => 'success', 'html' => $returnHTML);
        return response()->json($arr);
    }

    public function RemoveSpecialChar($str){
        return str_ireplace( array( '\'', '"', ',' , ';', '<', '>', '*' ), ' ', $str);
        }


    public function postStore(Request $request)
    {
        $this->resp = $this->bankstate->postStore($request);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($PK_NO) {

        $this->resp = $this->bankstate->delete($PK_NO);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }


    public function  postDeleteBulk(Request $request) {

        $this->resp = $this->bankstate->postDeleteBulk($request);
        return response()->json($this->resp);
    }


    public function postDraftToSave(Request $request)
    {
        $this->resp = $this->bankstate->postDraftToSave($request);
        return response()->json($this->resp);
    }

    public function postMarkAsUsed(Request $request)
    {
        $this->resp = $this->bankstate->postMarkAsUsed($request);
        return response()->json($this->resp);
    }


    public function getVerification(Request $request)
    {
        $date = \Carbon\Carbon::today()->subDays(2);
        $date = date('Y-m-d',strtotime($date));

        $data['payment_acc_no'] = PaymentBankAcc::where('IS_ACTIVE','1')->get();
        $data['rows']           = BankStatement::where('IS_MATCHED', 0)->where('IS_DRAFT',0)->orderBy('PK_NO','ASC')->get();
        $data['verified']       = BankStatement::where('IS_MATCHED', 1)->where('IS_DRAFT',0)
                                    ->whereNotNull('F_ACC_BANK_TXN_NO')->where('MATCHED_ON','>', $date)
                                    ->orderBy('PK_NO','ASC')->get();
// dd($data['verified']);
        $data['payments']  = DB::table('ACC_BANK_TXN')
            ->select('ACC_BANK_TXN.PK_NO','ACC_BANK_TXN.TXN_DATE','ACC_BANK_TXN.AMOUNT_BUFFER','ACC_CUSTOMER_PAYMENTS.CUSTOMER_NAME','ACC_RESELLER_PAYMENTS.RESELLER_NAME','ACC_CUSTOMER_PAYMENTS.PAID_BY as CU_PAID_BY','ACC_RESELLER_PAYMENTS.PAID_BY as RE_PAID_BY','ACC_BANK_TXN.F_ACC_PAYMENT_BANK_NO','ACC_CUSTOMER_PAYMENTS.SLIP_NUMBER as CU_SLIP_NUMBER','ACC_CUSTOMER_PAYMENTS.PAYMENT_NOTE as CU_PAYMENT_NOTE','ACC_RESELLER_PAYMENTS.SLIP_NUMBER as RE_SLIP_NUMBER','ACC_RESELLER_PAYMENTS.PAYMENT_NOTE as RE_PAYMENT_NOTE','ACC_PAYMENT_BANK_ACC.BANK_NAME','ACC_PAYMENT_BANK_ACC.BANK_ACC_NAME','ACC_PAYMENT_BANK_ACC.BANK_ACC_NAME', 'ACC_PAYMENT_BANK_ACC.BANK_ACC_NO')
            ->leftJoin('ACC_CUSTOMER_PAYMENTS','ACC_CUSTOMER_PAYMENTS.PK_NO','ACC_BANK_TXN.F_CUSTOMER_PAYMENT_NO')
            ->leftJoin('ACC_RESELLER_PAYMENTS','ACC_RESELLER_PAYMENTS.PK_NO','ACC_BANK_TXN.F_RESELLER_PAYMENT_NO')
            ->leftJoin('ACC_PAYMENT_BANK_ACC','ACC_PAYMENT_BANK_ACC.PK_NO','ACC_BANK_TXN.F_ACC_PAYMENT_BANK_NO')
            ->where('ACC_BANK_TXN.IS_MATCHED',0)
            ->orderBy('ACC_BANK_TXN.TXN_DATE','DESC')
            ->get();

        return view('admin.bankstate.verification',compact('data'));
    }



    public function postVerify(Request $request)
    {
        $this->resp = $this->bankstate->postVerify($request);
        return response()->json($this->resp);

    }

    public function getUnVerify($id)
    {
        $this->resp = $this->bankstate->getUnVerify($id);
        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);

    }







/*
    public function getCreate()
    {
        return view('admin.account.create');
    }


    public function putUpdate(AccountRequest $request, $PK_NO) {

        $this->resp = $this->account->postUpdate($request, $PK_NO);

        return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    }



    */
}
