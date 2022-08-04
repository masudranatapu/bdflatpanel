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
        $ss_areas = DB::table('ss_area')->where('F_PARENT_AREA_NO', NULL)->get();
        $ss_agent_areas = DB::table('ss_agent_area')->where('F_USER_NO', $id)->first();
        $users = $id;
        // return $users;
        return view('admin.agents.area', compact('ss_areas', 'ss_agent_areas', 'users'));
    }
    public function agentAreaStore(Request $request)
    {
        //
        $this->validate($request, [
            'F_AREA_NO' => 'required',
        ]);
        // for F_AREA_NO 
        if ($request->F_AREA_NO) {
            $FAREANO = trim(implode(',', $request->F_AREA_NO), ',');
        } else {
            $FAREANO = NULL;
        }
        DB::table('ss_agent_area')->insert([
            'F_AREA_NO' => $FAREANO,
            'F_USER_NO' => $request->user_id,
        ]);
        return redirect()->back()->with('flashMessageSuccess','Agent area successfully added');
    }

    public function agentAreaUpdate(Request $request, $id)
    {
        //
        $this->validate($request, [
            'F_AREA_NO' => 'required',
        ]);
        // for F_AREA_NO 
        if ($request->F_AREA_NO) {
            $FAREANO = trim(implode(',', $request->F_AREA_NO), ',');
        } else {
            $FAREANO = NULL;
        }
        DB::table('ss_agent_area')->where('PK_NO', $id)->update([
            'F_AREA_NO' => $FAREANO,
        ]);
        return redirect()->back()->with('flashMessageSuccess','Agent area successfully added');
    }

}
