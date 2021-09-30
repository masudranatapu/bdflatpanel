<?php
namespace App\Repositories\Admin\Shopcategory;
use DB;
use Str;
use Auth;
use Image;
use App\Models\ShopCategory;
use App\Traits\RepoResponse;
class ShopcategoryAbstract implements ShopcategoryInterface
{
    use RepoResponse;
    protected $shopCategory;

    public function __construct(ShopCategory $shopCategory)
    {
 
        $this->shopCategory     = $shopCategory;
    }

    public function getPaginatedList($request, int $per_page = 20)
    {
        $data = $this->shopCategory->orderBy('NAME', 'ASC')->get();
        return $this->formatResponse(true, '', 'admin.shop_category.list', $data);
    }
    public function getSlugByText($txt){
        $str = strtolower($txt);
        $slug = Str::slug($str);
         // $slug  =str_replace(" ", "-", preg_replace('/([%\$!-_;:,.#\'"@*]+)/','-', $str));
        return $slug;
    }
    public function postStore($request)
    {
        DB::beginTransaction();
        try {
            $shopcategory                       = new ShopCategory();
            $shopcategory->NAME                 = $request->name;
            $shopcategory->IS_ACTIVE                = $request->is_active;
            $shopcategory->save();
        } catch (\Exception $e) {
            dd($e);
                     DB::rollback();
            return $this->formatResponse(false, $e, 'admin.shop.category.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Shop Category has been created successfully !', 'admin.shop.category.list');
    }


    public function findOrThrowException($id)
    {
        $data = $this->shopCategory->where('PK_NO', '=', $id)->first();
        if (!empty($data)) {
            return $this->formatResponse(true, '', 'admin.shop.category.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.shop.category.list', null);
    }
    public function postUpdate($request, $id)
    {
        DB::beginTransaction();
        try {
            $shopCategory = ShopCategory::find($id);
            $shopCategory->NAME                  = $request->name;
            $shopCategory->IS_ACTIVE             = $request->is_active;
            $shopCategory->update();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to update Shop Category !', 'admin.shop.category.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Category has been updated successfully !', 'admin.shop.category.list');
    }
    public function delete($id)
    {
        DB::begintransaction();
        try {
            $shopcategory = ShopCategory::find($id)->delete();
            // $category->IS_ACTIVE = 0;
            // $category->update();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete this shop category !', 'admin.shop.category.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully delete this Shop category !', 'admin.shop.category.list');
    }

    public function getAllHScodes($category_id){
        return $this->subcategory->select('hs.CODE as HS_CODE', 'hs.PK_NO as HS_PK_NO', 'hs.NARRATION as HS_NARRATION', 'PRD_SUB_CATEGORY.NAME as SCAT_NAME', 'PRD_SUB_CATEGORY.PK_NO as SCAT_PK_NO'  )->where('F_PRD_CATEGORY_NO', $category_id)->join('PRD_HS_CODE as hs', 'hs.F_PRD_SUB_CATEGORY_NO','=','PRD_SUB_CATEGORY.PK_NO')->get();
    }

}
