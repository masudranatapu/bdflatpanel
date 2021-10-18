<?php
namespace App\Repositories\Admin\Role;

use DB;
use App\User;
use App\Models\Auth;
use App\Models\Role;
use App\Traits\RepoResponse;
use App\Models\RolePermission;
use App\Models\PermissionGroup;

class RoleAbstract implements RoleInterface
{
    use RepoResponse;

    protected $role;

    protected $permGroup;

    public function __construct(Role $role, PermissionGroup $permGroup)
    {
        $this->role = $role;
        $this->permGroup = $permGroup;
    }

    public function getPaginatedList($request, int $per_page = 20)
    {
        $data = $this->role::select(
            'SA_ROLE.PK_NO',
            'NAME',
            'SA_ROLE.CREATED_AT',
            'FIRST_NAME',
            'LAST_NAME'
            )->leftJoin('SA_USER', 'SA_USER.PK_NO', 'SA_ROLE.CREATED_BY')
            ->get();

        return $this->formatResponse(true, '', 'admin.role', $data);
    }

    public function getAllGroups($status = 1, $order_by = 'PK_NO', $sort = 'asc')
    {
        return $this->permGroup->with('permissions')->where('STATUS', $status)->orderBy('NAME', 'ASC')->get();
    }

    public function postStore($request, $permissions)
    {
        DB::beginTransaction();

        try {
            $role = new Role();
            $role->NAME = $request['role_name'];
            $role->CREATED_BY = auth()->user()->PK_NO;
            $role->UPDATED_BY = 0;
            $role->STATUS = 1;

            if($role->save()) {
                $perm_string = ",dashboard,";

                if(count($permissions['permission'])){
                    $perm_string = implode(',',$permissions['permission']);
                    $perm_string = ','.$perm_string.',';
                }

                $rolePermission = new RolePermission();
                $rolePermission->F_ROLE_NO = $role->PK_NO;
                $rolePermission->PERMISSIONS = $perm_string;
                $rolePermission->save();
            }

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.role');
        }
        DB::commit();

        return $this->formatResponse(true, 'Role has been created successfully !', 'admin.role');
    }

    public function findOrThrowException($id)
    {
        $role = $this->role->with('permission')->find($id);

        if (! is_null($role)) return $role;
        throw new GeneralException('That role does not exist.');
    }

    public function postUpdate($request, int $id, $permissions)
    {
        DB::beginTransaction();

        try {
            $role = $this->findOrThrowException($id);

            $role->NAME = $request['role_name'];
            $role->UPDATED_BY = 1;

            //Update Role permission table
            $perm_string = ",dashboard,";
            if (empty($permissions)) {
                $role->permission->permissions = '';
                $role->push();
            }else if(count($permissions['permission'])) {
                $perm_string = implode(',', $permissions['permission']);
                $perm_string = ','.$perm_string.',';
                $role->permission->permissions = $perm_string;
                $role->push();
            }

        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, $e->getMessage(), 'admin.role');
        }

        DB::commit();

        return $this->formatResponse(true, 'Role has been updated successfully !', 'admin.role');
    }

    public function delete(int $id)
    {
        DB::begintransaction();
        try {
            User::where('auth_id', $id)->delete();
            Auth::where('id',$id)->delete();
            DB::commit();
            echo 'deleted successfully';
        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to delete admin role !', 'admin.role');
        }

        return $this->formatResponse(true, 'Successfully delete admin role !', 'admin.role');
    }
    public function getList()
    {
        return DB::table('SA_ROLE')->pluck('NAME', 'PK_NO');
    }
}
