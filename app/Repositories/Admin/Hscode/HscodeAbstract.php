<?php
namespace App\Repositories\Admin\Hscode;

use App\Models\Hscode;
use App\Traits\RepoResponse;
use DB;


class HscodeAbstract implements HscodeInterface
{
    use RepoResponse;

    protected $hscode;

    public function __construct(Hscode $hscode)
    {
        $this->hscode = $hscode;
    }

    public function getPaginatedList($request, int $per_page = 10)
    {
        $data = $this->hscode->select('PRD_HS_CODE.PK_NO as HS_PK_NO','PRD_HS_CODE.CODE as HS_CODE','PRD_HS_CODE.NARRATION as HS_NARRATION','scat.PK_NO as SCAT_PK_NO','scat.NAME as SCAT_NAME','cat.PK_NO as CAT_PK_NO','cat.NAME as CAT_NAME')
        ->join('PRD_SUB_CATEGORY as scat','scat.PK_NO','=','PRD_HS_CODE.F_PRD_SUB_CATEGORY_NO')
        ->join('PRD_CATEGORY as cat','cat.PK_NO','=','scat.F_PRD_CATEGORY_NO')
        ->get();
        return $this->formatResponse(true, '', 'admin.hscode.list', $data);
    }


    public function postStore($request)
    {
       
        DB::beginTransaction();

        try {
            $hscode                                = new Hscode();
            $hscode->F_PRD_SUB_CATEGORY_NO         = $request->subcategory;
            $hscode->CODE                          = $request->code;
            $hscode->NARRATION                     = $request->narration;
            $hscode->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e, 'admin.hscode.list');
        }
        DB::commit();

        return $this->formatResponse(true, 'HS code has been created successfully !', 'admin.hscode.list');
    }

    public function findOrThrowException($id)
    {
        $data = $this->hscode->where('PK_NO', '=', $id)->first();

        if (!empty($data)) {
            return $this->formatResponse(true, '', 'admin.hscode.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.hscode.list', null);
    }


    public function postUpdate($request, $id)
    {
        DB::beginTransaction();

        try {


            $this->hscode->where('PK_NO', $id)->update(
                [
                    'F_PRD_SUB_CATEGORY_NO' => $request->subcategory,
                    'CODE'                  => $request->code,
                    'NARRATION'             => $request->narration
                ]
            );

        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to update HS code !', 'admin.hscode.list');
        }

        DB::commit();

        return $this->formatResponse(true, 'HS code has been updated successfully !', 'admin.hscode.list');
    }

    public function delete($id)
    {
        DB::begintransaction();
        try {
            $this->hscode->where('PK_NO', $id)->delete();
           
       
        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to delete this action !', 'admin.hscode.list');
        }

         DB::commit();

        return $this->formatResponse(true, 'Successfully delete this action !', 'admin.hscode.list');
    }




}
