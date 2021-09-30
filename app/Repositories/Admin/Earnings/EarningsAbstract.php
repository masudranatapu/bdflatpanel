<?php
namespace App\Repositories\Admin\Earnings;

use DB;
use App\Models\Auth;
use App\Models\Agent;
use App\Models\UserGroup;
use App\Traits\RepoResponse;
use App\Models\AccountSource;
use App\Models\AuthUserGroup;
use App\Models\AdminUser as User;
use Illuminate\Support\Facades\Hash;

class EarningsAbstract implements EarningsInterface
{
    use RepoResponse;

    protected $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function getPaginatedList($request, int $per_page = 5)
    {
        $data = $this->agent->where('IS_ACTIVE',1)->orderBy('NAME', 'ASC')->get();
        //dd($data);
        return $this->formatResponse(true, '', 'admin.agent.index', $data);
    }

    public function postStore($request)
    {
        //dd($request);
        // $check_dup = Agent::where('NAME',$request->name)->first();
        // if ($check_dup !== null) {
        //     return $this->formatResponse(false, 'Duplicate entry for Payment Source !', 'admin.account.list');
        // }

        DB::beginTransaction();

        try {
            $agent                  = new Agent();
            $agent->NAME            = $request->name;
            $agent->MOBILE_NO       = $request->phone;
            $agent->ALTERNATE_NO    = $request->alt_phone;
            $agent->EMAIL           = $request->email;
            $agent->FB_ID           = $request->fb_id;
            $agent->IG_ID           = $request->ig_id;
            $agent->UKSHOP_ID       = $request->uk_id;
            $agent->UKSHOP_PASS     = bcrypt($request->uk_pass);
            $agent->IS_ACTIVE       = 1;
            $agent->save();

            $auth = new Auth();
            $auth->USERNAME     = $request->name;
            $auth->MOBILE_NO    = $request->phone;
            $auth->EMAIL        = $request->email;
            $auth->PASSWORD     = Hash::make($request->uk_pass);
            $auth->GENDER       = 1;
            $auth->CAN_LOGIN    = 1;
            $auth->STATUS       = 1;
            $auth->FIRST_NAME   = $request->name;
            $auth->F_AGENT_NO   = $agent->PK_NO;
            $auth->PROFILE_PIC_URL = url('media/images/profile/computer-icons-user-profile-clip-art.jpg');
            $auth->PROFILE_PIC  = 'computer-icons-user-profile-clip-art.jpg';
            $auth->save();

            $roleAuth               = new AuthUserGroup();
            $roleAuth->F_USER_NO    = $auth->id;
            $roleAuth->F_GROUP_NO   = 7;
            $roleAuth->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.agent.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'Agent has been created successfully !', 'admin.agent.list');
    }

    public function postUpdate($request, $PK_NO)
    {
        DB::beginTransaction();

        try {
            $agent                  = Agent::where('PK_NO', $PK_NO)->first();
            $auths                  = Auth::where('F_AGENT_NO',$PK_NO)->first();

            $agent->NAME            = $request->name;
            $agent->MOBILE_NO       = $request->phone;
            $agent->ALTERNATE_NO    = $request->alt_phone;
            $agent->EMAIL           = $request->email;
            $agent->FB_ID           = $request->fb_id;
            $agent->IG_ID           = $request->ig_id;
            $agent->UKSHOP_ID       = $request->uk_id;
            $auths->EMAIL           = $request->email;

            if (isset($request->uk_pass) && !empty($request->uk_pass)) {
                $agent->UKSHOP_PASS = bcrypt($request->uk_pass);
                $auths->PASSWORD    = bcrypt($request->uk_pass);
            }
            $agent->save();
            $auths->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.agent.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Agent Information has been Updated successfully', 'admin.agent.list');
    }

    public function delete($PK_NO)
    {
        $agent = Agent::where('PK_NO',$PK_NO)->first();
        $agent->IS_ACTIVE = 0;
        if ($agent->update()) {
            return $this->formatResponse(true, 'Successfully deleted Agent Information', 'admin.agent.list');
        }
        return $this->formatResponse(false,'Unable to delete Agent Information','admin.agent.list');
    }
}
