<?php
namespace App\Repositories\Admin\SubCategory;
use DB;
use Str;
use Image;
use App\Models\SubCategory;
use App\Traits\RepoResponse;

class SubCategoryAbstract implements SubCategoryInterface
{
    use RepoResponse;
    protected $sub_category;

    public function __construct(SubCategory $sub_category)
    {
        $this->sub_category = $sub_category;
    }

    public function getPaginatedList($request, int $per_page = 10)
    {
        $data = $this->sub_category->where('IS_ACTIVE',1)->orderBy('NAME', 'ASC')->get();
        return $this->formatResponse(true, '', 'admin.sub_category.list', $data);
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
            $sub_category                           = new SubCategory();
            $sub_category->F_PRD_CATEGORY_NO        = $request->category;
            $sub_category->NAME                     = $request->name;
            $sub_category->CODE                     = $request->code;
            if(!empty($request->url_slug)){
                $sub_category->URL_SLUG             = $this->getSlugByText($request->url_slug);
            }
            else {
                $sub_category->URL_SLUG             = $this->getSlugByText($request->name);
            }
            $sub_category->HS_PREFIX                = $request->hs_prefix;
            if(!is_null($request->file('thumbnail_image')))
            {
                $sub_category->THUMBNAIL_PATH       = $this->uploadImage($request->thumbnail_image);
            }
            if(!is_null($request->file('banner_image')))
            {
                $sub_category->BANNER_PATH          = $this->uploadImage($request->banner_image);
            }
            if(!is_null($request->file('icon')))
            {
                $sub_category->ICON                 = $this->uploadImage($request->icon);
            }
            $sub_category->ORDER_ID                 = SubCategory::max('ORDER_ID')+1;
            $sub_category->COMMENTS                 = $request->comment;
            $sub_category->IS_FEATURE               = $request->is_feature;
            $sub_category->META_TITLE               = $request->meta_title;
            $sub_category->META_KEYWARDS            = $request->meta_keywards;
            $sub_category->META_DESCRIPTION         = $request->meta_description;
            $sub_category->IS_ACTIVE                = $request->is_active ?? 1;
            $sub_category->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, $e, 'admin.sub_category.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Sub Category has been created successfully !', 'admin.sub_category.list');
    }

    public function uploadImage($image)
    {
      if($image)
      {
          $filename = $image->getClientOriginalExtension();
          $destinationPath1 = 'media/images/banner';
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
        $data = $this->sub_category->where('PK_NO', '=', $id)->first();
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.sub_category.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.sub_category.list', null);
    }
    public function postUpdate($request, $id)
    {
        DB::beginTransaction();
        try {
            $scat                       = SubCategory::find($id);
            $scat->F_PRD_CATEGORY_NO    = $request->category;
            $scat->NAME                 = $request->name;
            $scat->CODE                     = $request->code;
            if(!empty($request->url_slug)){
                $scat->URL_SLUG             = $this->getSlugByText($request->url_slug);
            }
            else {
                $scat->URL_SLUG             = $this->getSlugByText($request->name);
            }
            $scat->HS_PREFIX                = $request->hs_prefix;
            if(!is_null($request->file('thumbnail_image')))
            {
                $scat->THUMBNAIL_PATH       = $this->uploadImage($request->thumbnail_image);
            }
            if(!is_null($request->file('banner_image')))
            {
                $scat->BANNER_PATH          = $this->uploadImage($request->banner_image);
            }
            if(!is_null($request->file('icon')))
            {
                $scat->ICON                 = $this->uploadImage($request->icon);
            }

           $scat->COMMENTS                 = $request->comment;
           $scat->IS_FEATURE               = $request->is_feature;
           $scat->META_TITLE               = $request->meta_title;
           $scat->META_KEYWARDS            = $request->meta_keywards;
           $scat->META_DESCRIPTION         = $request->meta_description;
           $scat->IS_ACTIVE                = $request->is_active ?? 1;
        $scat->update();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to update Sub Category !', 'admin.sub_category.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Sub Category has been updated successfully !', 'admin.sub_category.list');
    }


    public function delete($id)
    {
        DB::begintransaction();
        try {
            SubCategory::find($id)->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete this action !', 'admin.sub_category.list');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully delete this action !', 'admin.sub_category.list');
    }
}
