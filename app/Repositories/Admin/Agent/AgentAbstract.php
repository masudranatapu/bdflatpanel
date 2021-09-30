<?php

namespace App\Repositories\Admin\Agent;

use Illuminate\Support\Facades\DB;
use App\Models\Auth;
use App\Models\Agent;
use App\Traits\RepoResponse;
use App\Models\AuthUserGroup;
use Illuminate\Support\Facades\Hash;

class AgentAbstract implements AgentInterface
{
    use RepoResponse;

    protected $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function getPaginatedList($request, int $per_page = 5): object
    {
        $data = $this->agent->orderBy('NAME', 'ASC')->get();
        return $this->formatResponse(true, '', 'admin.agent.index', $data);
    }

    public function getAgent(int $id): object
    {
        $agent = Agent::with(['info'])->find($id);
        return $this->formatResponse(true, '', '', $agent);
    }

    public function postStore($request): object
    {
        DB::beginTransaction();
        try {
            $agent = new Agent();
            $agent->NAME = $request->name;
            $agent->MOBILE_NO = $request->phone;
            $agent->EMAIL = $request->email;
            $agent->STATUS = $request->status;
            $agent->IS_FEATURE = $request->is_feature;
            $agent->PASSWORD = Hash::make($request->pass);

            if ($request->hasFile('images')) {
                $image = $request->file('images')[0];
                $image_name = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_path = 'uploads/agents/';
                $image->move(public_path($image_path), $image_name);
                $agent->PROFILE_PIC = $image_name;
                $agent->PROFILE_PIC_URL = $image_path . $image_name;
            }

            $agent->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.agents.list');
        }

        DB::commit();
        return $this->formatResponse(true, 'Agent has been created successfully !', 'admin.agents.list');
    }

    public function postUpdate($request, $id): object
    {
        DB::beginTransaction();
        try {
            $agent = Agent::find($id);
            $agent->USER_TYPE = 5;
            $agent->NAME = $request->name;
            $agent->MOBILE_NO = $request->phone;
            $agent->EMAIL = $request->email;
            $agent->STATUS = $request->status;
            $agent->IS_FEATURE = $request->is_feature;

            if ($request->hasFile('images')) {
                $image = $request->file('images')[0];
                $image_name = uniqid() . '.' . $image->getClientOriginalExtension();
                $image_path = 'uploads/agents/';
                $image->move(public_path($image_path), $image_name);
                $agent->PROFILE_PIC = $image_name;
                $agent->PROFILE_PIC_URL = $image_path . $image_name;
            }

            if (isset($request->pass)) {
                $agent->PASSWORD = Hash::make($request->pass);
            }
            $agent->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.agents.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Agent Information has been updated successfully', 'admin.agents.list');
    }

    public function delete($PK_NO)
    {
        $agent = Agent::where('PK_NO', $PK_NO)->first();
        $agent->IS_ACTIVE = 0;
        if ($agent->update()) {
            return $this->formatResponse(true, 'Successfully deleted Agent Information', 'admin.agent.list');
        }
        return $this->formatResponse(false, 'Unable to delete Agent Information', 'admin.agent.list');
    }
}
