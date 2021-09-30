<?php
namespace App\Repositories\Admin\PropertyCategory;
use DB;
use Str;
use Auth;
use Image;
use App\Models\PropertyCategory;
use App\Traits\RepoResponse;
class PropertyCategoryAbstract implements PropertyCategoryInterface
{
    use RepoResponse;
    protected $category;
    public function __construct(PropertyCategory $category)
    {
        $this->category     = $category;
    }
    public function getPaginatedList($request, int $per_page = 20)
    {
        $data = $this->category->where('IS_ACTIVE',1)->orderBy('ORDER_ID', 'DESC')->get();
        return $this->formatResponse(true, '', 'admin.property-category.index', $data);
    }
    public function getSlugByText($txt){
        $str = strtolower($txt);
        $slug = Str::slug($str);
         // $slug  =str_replace(" ", "-", preg_replace('/([%\$!-_;:,.#\'"@*]+)/','-', $str));
        return $slug;
    }
    public function postStore($request)
    {
        //dd($request->All());
        DB::beginTransaction();
        try {
            $category                           = new PropertyCategory();
            $category->PROPERTY_TYPE            = $request->category_name;
            if(!empty($request->category_name)){
                $category->URL_SLUG             = $this->getSlugByText($request->category_name);
            }
            if(!is_null($request->file('image')))
            {
                $category->IMG_PATH       = $this->uploadImage($request->image);
            }
            if(!is_null($request->file('icon')))
            {
                $category->ICON_PATH          = $this->uploadImage($request->icon);
            }
            $category->META_TITLE               = $request->meta_title;
            $category->META_DESC                = $request->meta_description;
            $category->BODY_DESC                = $request->body_description;
            $category->CATEGORY_URL             = $request->url;
            $category->ORDER_ID                 = $request->order;
            $category->save();
        } catch (\Exception $e) {
            dd($e);
                     DB::rollback();
            return $this->formatResponse(false, $e, 'admin.property.category');
        }
        DB::commit();
        return $this->formatResponse(true, 'Property category has been created successfully !', 'admin.property.category');
    }


    public function uploadImage($image)
    {
        $imageUrl = null;
      if($image)
      {
        $file_name  = 'img_'. date('dmY'). '_' .uniqid(). '.' . $image->getClientOriginalExtension();
        $imageUrl   = '/media/images/property-category/'.$file_name;
        $image->move(public_path().'/media/images/property-category/',$file_name);
      }
      return $imageUrl;
    }

    public function findOrThrowException($id)
    {
        $data = $this->category->where('PK_NO', '=', $id)->first();
        if (!empty($data)) {
            return $this->formatResponse(true, '', 'property.category.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.property.category', null);
    }
    public function postUpdate($request, $id)
    {
        DB::beginTransaction();
        try {
            $category = PropertyCategory::find($id);
            $category->PROPERTY_TYPE            = $request->category_name;
            if(!empty($request->category_name)){
                $category->URL_SLUG             = $this->getSlugByText($request->category_name);
            }
            if(!is_null($request->file('image')))
            {
                $category->IMG_PATH       = $this->uploadImage($request->image);
            }
            if(!is_null($request->file('icon')))
            {
                $category->ICON_PATH          = $this->uploadImage($request->icon);
            }
            $category->META_TITLE               = $request->meta_title;
            $category->META_DESC                = $request->meta_description;
            $category->BODY_DESC                = $request->body_description;
            $category->CATEGORY_URL             = $request->url;
            $category->ORDER_ID                 = $request->order;
            $category->update();
        } catch (\Exception $e) {
            DB::rollback();
            dd($e);
            return $this->formatResponse(false, 'Unable to update category !', 'admin.property.category');
        }
        DB::commit();
        return $this->formatResponse(true, 'Property category has been updated successfully !', 'admin.property.category');
    }
    public function delete($id)
    {
        DB::begintransaction();
        try {
            $category = Category::find($id)->delete();
            // $category->IS_ACTIVE = 0;
            // $category->update();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete this category !', 'product.category.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully delete this category !', 'product.category.list');
    }

    public function getAllHScodes($category_id){
        return $this->subcategory->select('hs.CODE as HS_CODE', 'hs.PK_NO as HS_PK_NO', 'hs.NARRATION as HS_NARRATION', 'PRD_SUB_CATEGORY.NAME as SCAT_NAME', 'PRD_SUB_CATEGORY.PK_NO as SCAT_PK_NO'  )->where('F_PRD_CATEGORY_NO', $category_id)->join('PRD_HS_CODE as hs', 'hs.F_PRD_SUB_CATEGORY_NO','=','PRD_SUB_CATEGORY.PK_NO')->get();
    }

}
