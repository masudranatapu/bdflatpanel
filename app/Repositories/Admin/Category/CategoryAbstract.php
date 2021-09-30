<?php
namespace App\Repositories\Admin\Category;
use DB;
use Str;
use Auth;
use Image;
use App\Models\Category;
use App\Models\SubCategory;
use App\Traits\RepoResponse;
class CategoryAbstract implements CategoryInterface
{
    use RepoResponse;
    protected $category;
    protected $subcategory;
    public function __construct(Category $category, SubCategory $subcategory)
    {
        $this->category     = $category;
        $this->subcategory  = $subcategory;
    }
    public function getPaginatedList($request, int $per_page = 20)
    {
        $data = $this->category->where('IS_ACTIVE',1)->orderBy('NAME', 'ASC')->get();
        if ($data) {
            foreach ($data as $key => $value) {
               $value->allHScodes = $this->getAllHScodes($value->PK_NO);
            }
        }
        return $this->formatResponse(true, '', 'product.category.list', $data);
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
            $category                           = new Category();
            $category->NAME                     = $request->name;
            $category->CODE                     = $request->code;
            if(!empty($request->url_slug)){
                $category->URL_SLUG             = $this->getSlugByText($request->url_slug);
            }
            else {
                $category->URL_SLUG             = $this->getSlugByText($request->name);
            }
            $category->HS_PREFIX                = $request->hs_prefix;
            if(!is_null($request->file('thumbnail_image')))
            {
                $category->THUMBNAIL_PATH       = $this->uploadImage($request->thumbnail_image);
            }
            if(!is_null($request->file('banner_image')))
            {
                $category->BANNER_PATH          = $this->uploadImage($request->banner_image);
            }
            if(!is_null($request->file('icon')))
            {
                $category->ICON                 = $this->uploadImage($request->icon);
            }
            $category->ORDER_ID                 = Category::max('ORDER_ID')+1;
            $category->COMMENTS                 = $request->comment;
            $category->IS_FEATURE               = $request->is_feature;
            $category->META_TITLE               = $request->meta_title;
            $category->META_KEYWARDS            = $request->meta_keywards;
            $category->META_DESCRIPTION         = $request->meta_description;
            $category->IS_ACTIVE                = $request->is_active;
            $category->save();
        } catch (\Exception $e) {
            dd($e);
                     DB::rollback();
            return $this->formatResponse(false, $e, 'product.category.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Category has been created successfully !', 'product.category.list');
    }


    public function uploadImage($image)
    {
      if($image)
      {
          $filename = $image->getClientOriginalExtension();
          $destinationPath1 = '/media/images/banner';
          if (!file_exists($destinationPath1)) {
              mkdir($destinationPath1, 0755, true);
          }
        $img = Image::make($image->getRealPath());
        $file_name1 = 'prod_'. date('dmY'). '_' .uniqid().'.'.$filename;
        Image::make($img)->save($destinationPath1.'/'.$file_name1);
        $imageUrl1 = $destinationPath1 .'/'. $file_name1;
      }
      return $imageUrl1;
    }

    public function findOrThrowException($id)
    {
        $data = $this->category->where('PK_NO', '=', $id)->first();
        if (!empty($data)) {
            return $this->formatResponse(true, '', 'admin.category.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.category.list', null);
    }
    public function postUpdate($request, $id)
    {
        DB::beginTransaction();
        try {
            $category = Category::find($id);
            $category->NAME                     = $request->name;
            if(!empty($request->url_slug)){
                $category->URL_SLUG             = $this->getSlugByText($request->url_slug);
            }
            else {
                $category->URL_SLUG             = $this->getSlugByText($request->name);
            }
            $category->HS_PREFIX                = $request->hs_prefix;
            if(!is_null($request->file('thumbnail_image')))
            {
                $category->THUMBNAIL_PATH       = $this->uploadImage($request->thumbnail_image);
            }
            if(!is_null($request->file('banner_image')))
            {
                $category->BANNER_PATH          = $this->uploadImage($request->banner_image);
            }
            if(!is_null($request->file('icon')))
            {
                $category->ICON                 = $this->uploadImage($request->icon);
            }
           // $category->ORDER_ID                 = Category::max('ORDER_ID')+1;
            $category->COMMENTS                 = $request->comment;
            $category->IS_FEATURE               = $request->is_feature;
            $category->META_TITLE               = $request->meta_title;
            $category->META_KEYWARDS            = $request->meta_keywards;
            $category->META_DESCRIPTION         = $request->meta_description;
            $category->IS_ACTIVE                = $request->is_active;
            $category->update();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to update Category !', 'product.category.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Category has been updated successfully !', 'product.category.list');
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
