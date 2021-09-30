<?php

namespace App\Http\Controllers\Admin;

use App\Models\Agent;
use App\Models\Earning;
use App\Repositories\Admin\Earnings\EarningsInterface;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use App\Http\Requests\Admin\AgentRequest;
use App\Repositories\Admin\Agent\AgentInterface;

class EarningsController extends BaseController
{
    protected $earnings;

    public function __construct(EarningsInterface $earnings, Earning $earningsmodel)
    {
        $this->earnings        = $earnings;
        $this->earningsmodel   = $earningsmodel;
    }

    public function getIndex(Request $request)
    {
        /*$this->resp = $this->agent->getPaginatedList($request, 20);
        return view('admin.agent.index')->withRows($this->resp->data);*/
        return view('admin.earnings.index');
    }

    public function getCreate() {

        return view('admin.agents.create');
    }

    public function postStore(Request $request) {

        $this->resp = $this->earnings->postStore($request);

        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit($id)
    {
        $agent    = Agent::find($id);

        if (!$agent) {
            return redirect()->route('admin.agent.list');
        }
        return view('admin.agent.edit')->withAgent($agent);
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

}
