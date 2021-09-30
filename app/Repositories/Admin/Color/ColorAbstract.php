<?php
namespace App\Repositories\Admin\Color;
use App\Models\Color;
use App\Traits\RepoResponse;
use DB;

class ColorAbstract implements ColorInterface
{
    use RepoResponse;

    protected $color;

    public function __construct(Color $color)
    {
        $this->color = $color;
    }

    public function getPaginatedList($request, int $per_page = 20)
    {
        $data = $this->color->where('IS_ACTIVE',1)->orderBy('NAME','ASC')->select('PK_NO','CODE','NAME','F_BRAND')->get();

        return $this->formatResponse(true, '', 'admin.product.color.list', $data);
    }

    public function findOrThrowException($id)
    {
        $data = $this->color->where('PK_NO', '=', $id)->first();

        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.product.color.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'admin.brand.list', null);
    }


    public function postStore($request)
    {
        $brand = DB::table('PRD_BRAND')->select('NAME')->where('PK_NO', '=', $request->brand)->first();


        DB::beginTransaction();

        try {
            $color = new Color();
            $color->NAME            = $request->name;
            $color->F_BRAND         = $request->brand;
            $color->save();

        } catch (\Exception $e) {

            dd($e);

            DB::rollback();
            return $this->formatResponse(false, 'Unable to create product color !', 'admin.product.color.list');

        }
        DB::commit();

        return $this->formatResponse(true, 'Color has been created successfully !', 'admin.product.color.list');
    }


    public function delete($id)
    {
        DB::begintransaction();
        try {
            $color = Color::find($id)->delete();
            // $color->IS_ACTIVE = 0;
            // $color->update();
                   
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete this action !', 'admin.product.color.list');
        }
         DB::commit();

        return $this->formatResponse(true, 'Color has been deleted successfully !', 'admin.product.color.list');
    }


    public function postUpdate($request, $id){
        DB::beginTransaction();

        try {

            $color = Color::find($id);
            $color->F_BRAND = $request->brand;
            $color->NAME = $request->name;
            $color->update();

        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to update color !', 'admin.product.color.list');
        }

        DB::commit();
        return $this->formatResponse(true, 'Color has been updated successfully !', 'admin.product.color.list');

    }







}
