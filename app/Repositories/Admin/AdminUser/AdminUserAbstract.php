<?php
namespace App\Repositories\Admin\AdminUser;

use App\Models\AdminUser as User;
use App\Traits\RepoResponse;
use App\Repositories\Admin\Auth\AuthAbstract;
use App\Models\Auth;
use App\Models\AuthUserGroup;
use App\Models\UserGroup;
use DB;
use File;

class AdminUserAbstract implements AdminUserInterface
{
    use RepoResponse;

    protected $user;
    protected $auth;

    public function __construct(User $user, AuthAbstract $auth)
    {
        $this->user = $user;
        $this->auth = $auth;
    }

    public function getPaginatedList($request)
    {
        $data = Auth::where('USER_TYPE','!=',1)
            ->join('SA_USER_GROUP_USERS', 'SA_USER_GROUP_USERS.F_USER_NO', 'SA_USER.PK_NO')
            ->Join ('SA_USER_GROUP_ROLE', 'SA_USER_GROUP_ROLE.F_USER_GROUP_NO', 'SA_USER_GROUP_USERS.F_GROUP_NO')
            ->Join ('SA_ROLE', 'SA_ROLE.PK_NO', 'SA_USER_GROUP_ROLE.F_ROLE_NO')
            ->Join ('SA_USER_GROUP', 'SA_USER_GROUP.PK_NO', 'SA_USER_GROUP_USERS.F_GROUP_NO')
            ->select('SA_USER.USERNAME','SA_USER.EMAIL','SA_USER.MOBILE_NO','SA_USER.CAN_LOGIN','SA_USER.FIRST_NAME','SA_USER.LAST_NAME','SA_USER.DESIGNATION','SA_USER.PK_NO','SA_USER.PROFILE_PIC_URL','SA_USER.STATUS', 'SA_USER_GROUP.GROUP_NAME','SA_ROLE.NAME')->get();

        return $this->formatResponse(true, '', 'admin', $data);
    }

    public function postStore($request)
    {
        DB::beginTransaction();

        try {
            $auth = $this->auth->postStore($request);
            if($request->user_group != "")
            {
                $roleAuth               = new AuthUserGroup();
                $roleAuth->F_USER_NO    = $auth->PK_NO;
                $roleAuth->F_GROUP_NO   = $request->user_group;
                $roleAuth->save();
            }else{
                return $this->formatResponse(false, 'Please select User Group', 'admin.admin-user');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.admin-user');
        }

        DB::commit();

        return $this->formatResponse(true, 'Admin User has been created successfully !', 'admin.admin-user');
    }

    public function postUpdate($request, int $id, string $type = null)
    {
        DB::beginTransaction();
        try {
            $this->auth->postUpdate($request, $id,$type);

            if($request->user_group != "")
            {
                AuthUserGroup::where('F_USER_NO',$id)->update(['F_GROUP_NO' => $request->user_group]);
            }
            /*$roleAuth = AuthUserGroup::where('Auth_id',$id)->first();
            $roleAuth->role_id = $request->role;
            $roleAuth->update();*/
        } catch (\Exception $e) {
            DB::rollback();
            echo '<pre>';
            echo '======================<br>';
            print_r($e->getMessage());
            echo '<br>======================<br>';
            exit();
            return $this->formatResponse(false, 'Unable to update user !', 'admin.admin-user');
        }
        DB::commit();
        return $this->formatResponse(true, 'User has been updated successfully !', 'admin.admin-user');
    }

    public function getShow(int $id)
    {
        $data =  Auth::join('SA_USER_GROUP_USERS','SA_USER_GROUP_USERS.F_USER_NO','SA_USER.PK_NO')
            ->select('SA_USER.*','SA_USER.STATUS as auth_status','SA_USER_GROUP_USERS.F_GROUP_NO')
            ->where('SA_USER.PK_NO', $id)
            ->first();

        if (!empty($data)) {
            return $this->formatResponse(true, '', 'admin.admin-user.admin-user', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.admin-user', null);
    }

    public function delete(int $id)
    {
        DB::begintransaction();
        try {
            AuthUserGroup::where('F_USER_NO',$id)->delete();
            Auth::where('PK_NO',$id)->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete admin user !', 'admin.admin-user');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully delete admin user !', 'admin.admin-user');
    }

    public function getSearchList($request)
    {
        $string = trim($request->search_string);
        $data = Auth::where('USER_TYPE','!=',1 )
                ->where('SA_USER.EMAIL', 'LIKE', '%' . $string . '%')->orWhere('SA_USER.USERNAME', 'LIKE', '%' . $string . '%')
                ->join('SA_USER_GROUP_USERS', 'SA_USER_GROUP_USERS.F_USER_NO', 'SA_USER.PK_NO')
                ->join('SA_USER_GROUP', 'SA_USER_GROUP.PK_NO', 'SA_USER_GROUP_USERS.F_GROUP_NO')
                ->join ('SA_USER_GROUP_ROLE', 'SA_USER_GROUP_ROLE.F_USER_GROUP_NO', 'SA_USER_GROUP_USERS.F_GROUP_NO')
                ->join ('SA_ROLE', 'SA_ROLE.PK_NO', 'SA_USER_GROUP_ROLE.F_ROLE_NO')
                ->select('SA_USER.USERNAME','SA_USER.EMAIL','SA_USER.MOBILE_NO','SA_USER.CAN_LOGIN','SA_USER.FIRST_NAME','SA_USER.LAST_NAME','SA_USER.DESIGNATION','SA_USER.PK_NO','SA_USER.PROFILE_PIC_URL','SA_USER.STATUS', 'SA_USER_GROUP.GROUP_NAME','SA_ROLE.NAME')->get();
        //prixt($data,1);
        return $this->formatResponse(true, '', 'admin', $data);
    }
}
