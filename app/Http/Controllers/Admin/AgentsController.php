<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agent;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AgentRequest;
use App\Repositories\Admin\Agent\AgentInterface;
use DB;

class AgentsController extends BaseController
{
    protected $agent;
    protected $resp;

    public function __construct(AgentInterface $agent)
    {
        parent::__construct();
        $this->agent = $agent;
    }

    public function getIndex(Request $request)
    {
        return view('admin.agents.index');
    }

    public function getCreate()
    {
        return view('admin.agents.create');
    }

    public function postStore(AgentRequest $request)
    {
        $this->resp = $this->agent->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit($id)
    {
        $agent = Agent::find($id);
        $payment_method = PaymentMethod::orderBy('NAME')->pluck('NAME', 'PK_NO');

        if (!$agent) {
            return redirect()->route('admin.agents.list');
        }
        return view('admin.agents.edit')->withAgent($agent)->withPayment($payment_method);
    }

    public function postUpdate(AgentRequest $request, $id)
    {
        $this->resp = $this->agent->postUpdate($request, $id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getDelete($id)
    {
        $this->resp = $this->agent->delete($id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEarnings($id)
    {
        $data['agent'] = $this->agent->getAgent($id)->data;
        return view('admin.agents.earnings', compact('data'));
    }

    public function getWithdrawCredit()
    {
        $data = [];
        $data['payment_method'] = PaymentMethod::where('IS_ACTIVE', 1)->pluck('NAME', 'PK_NO');
        return view('admin.agents.withdraw', compact('data'));
    }

    public function getArea($id){
        $areas = DB::table('SS_AREA')->where('F_PARENT_AREA_NO', NULL)->orderBy('AREA_NAME', 'ASC')->get();
        // return $ss_areas;
        $agent_areas = DB::table('SS_AGENT_AREA')
        ->select('SS_AGENT_AREA.F_AREA_NO','SS_AGENT_AREA.F_USER_NO','SS_AGENT_AREA.PK_NO', 'SS_AREA.AREA_NAME')
        ->where('SS_AGENT_AREA.F_USER_NO', $id)
        ->leftJoin('SS_AREA', 'SS_AREA.PK_NO','SS_AGENT_AREA.F_AREA_NO')
        ->get();

        $area_arr = array();
        if($agent_areas){
            foreach ($agent_areas as $key => $value) {
                if($value){
                    array_push($area_arr,$value->F_AREA_NO);
                }
            }
        }
        $user = DB::table('WEB_USER')->where('PK_NO', $id)->first();

        return view('admin.agents.area', compact('areas', 'agent_areas', 'user','area_arr'));
    }

    public function agentAreaUpdate(Request $request, $id)
    {
        //
        $this->validate($request, [
            'area_no' => 'required',
            'user_id' => 'required',
        ]);
        // for F_AREA_NO

        if ($request->area_no) {
            foreach ($request->area_no as $key => $value) {
               $check = DB::table('SS_AGENT_AREA')->where('F_USER_NO',$request->user_id)->where('F_AREA_NO',$value)->first();

               if($check == null){
                DB::table('SS_AGENT_AREA')->insert([
                    'F_AREA_NO' => $value,
                    'F_USER_NO' => $request->user_id,
                ]);

               }
            }

        }

        return redirect()->back()->with('flashMessageSuccess','Agent area successfully added');
    }

    public function agentAreaDelete($id)
    {
        DB::table('SS_AGENT_AREA')->where('PK_NO', $id)->delete();
        return redirect()->back()->with('flashMessageSuccess','Agent area successfully added');
    }


}
