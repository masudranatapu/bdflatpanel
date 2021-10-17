<?php
namespace App\Repositories\Admin\Permission;

use App\Models\Permission;
use App\Traits\RepoResponse;
use DB;

class PermissionAbstract implements PermissionInterface
{
    use RepoResponse;

    protected $permission;

    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    public function getPaginatedList($request, int $per_page = 20)
    {
        $data = $this->permission->select('SA_PERMISSION_GROUP_DTL.*','SA_PERMISSION_GROUP.NAME as GROUP_NAME')->join('SA_PERMISSION_GROUP','SA_PERMISSION_GROUP.PK_NO', 'SA_PERMISSION_GROUP_DTL.F_PERMISSION_GROUP_NO')->orderBy('SA_PERMISSION_GROUP.NAME')->get();

        return $this->formatResponse(true, '', '', $data);
    }

    public function getList() {
        return DB::table('SA_PERMISSION_GROUP')->pluck('NAME', 'PK_NO');
    }

    public function postStore($request)
    {
        DB::beginTransaction();

        try {
            $permission = new Permission();
            $permission->NAME = $request->permission_slug;
            $permission->DISPLAY_NAME = $request->display_name;
            $permission->F_PERMISSION_GROUP_NO = $request->permission_group;
            $permission->STATUS = 1;
            $permission->save();

        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to create permission !', 'admin.permission');
        }

        DB::commit();

        return $this->formatResponse(true, 'Permission has been created successfully !', 'admin.permission');
    }

    public function postUpdate($request, int $id)
    {
        DB::beginTransaction();

        try {
            $this->permission->where('PK_NO', $id)->update(['NAME'=>$request->permission_slug,'DISPLAY_NAME'=>$request->display_name,'F_PERMISSION_GROUP_NO'=>$request->permission_group,'STATUS'=>1]);
        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to update permission !', 'admin.permission');
        }

        DB::commit();

        return $this->formatResponse(true, 'Permission has been updated successfully !', 'admin.permission');
    }

    public function getShow(int $id)
    {
        $data =  Permission::find($id);

        if (!empty($data)) {
            return $this->formatResponse(true, '', 'admin.permission', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.permission', null);
    }

    public function delete(int $id)
    {
        DB::begintransaction();
        try {
            Permission::where('PK_NO', $id)->delete();
            DB::commit();
            echo 'deleted successfully';
        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to delete this action !', 'admin.permission');
        }

        return $this->formatResponse(true, 'Successfully delete this action !', 'admin.permission');
    }

}
