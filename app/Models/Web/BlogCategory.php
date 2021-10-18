<?php
namespace App\Models\Web;
use Illuminate\Support\Str;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use RepoResponse;
    protected $table        = 'WEB_ARTICLE_CATEGORY';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;


    public function getPaginatedList($request, int $per_page = 2000)
    {
        $data = $this->where('IS_ACTIVE',1)->get();
        return $this->formatResponse(true, '', 'web.blog.category', $data);
    }
    public function getShow(int $id)
    {
        $data =  BlogCategory::find($id);
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'web.blog.category', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'web.blog.category', null);
    }

    public function postStore($request)
    {
        DB::beginTransaction();
        try {
            $category                                       = new BlogCategory();
            $category->NAME                                 = $request->name;
            $str                                            = strtolower($request->name);
            $category->URL_SLUG                             = Str::slug($str);
            $category->IS_ACTIVE                            = $request->is_active;
            $category->ORDER_ID                             = BlogCategory::max('ORDER_ID')+1;
            $category->META_KEYWARDS                        = $request->meta_keywards;
            $category->META_DESCRIPTION                     = $request->meta_description;
            if ($request->file('banner')) {
                $image          = $request->file('banner');
               $filename = $image->getClientOriginalExtension();
                $destinationPath1 = 'media/images/banner';
                if (!file_exists($destinationPath1)) {
                    mkdir($destinationPath1, 0755, true);
                }
                 $img = Image::make($image->getRealPath());
                $file_name1 = 'prod_'. date('dmY'). '_' .uniqid().'.'.$filename;
                Image::make($img)->save($destinationPath1.'/'.$file_name1);
                $category->BANNER = $destinationPath1 .'/'. $file_name1;
            }
            $category->save();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create Blog Category !', 'web.blog.category.create');
        }
        DB::commit();
        return $this->formatResponse(true, 'Blog Category has been created successfully !', 'web.blog.category',$category->PK_NO);
    }

     public function postUpdate($request)
    {
        DB::beginTransaction();
        try {
            $category                                       = BlogCategory::findOrFail($request->id);
            $category->NAME                                 = $request->name;
            $str                                            = strtolower($request->name);
            $category->URL_SLUG                             = Str::slug($str);
            $category->IS_ACTIVE                            = $request->is_active;
            // $category->ORDER_ID                             = BlogCategory::max('ORDER_ID')+1;
            $category->META_KEYWARDS                        = $request->meta_keywards;
            $category->META_DESCRIPTION                     = $request->meta_description;
            if ($request->file('banner')) {
                $image          = $request->file('banner');
               $filename = $image->getClientOriginalExtension();
                $destinationPath1 = 'media/images/banner';
                if (!file_exists($destinationPath1)) {
                    mkdir($destinationPath1, 0755, true);
                }
                 $img = Image::make($image->getRealPath());
                $file_name1 = 'prod_'. date('dmY'). '_' .uniqid().'.'.$filename;
                Image::make($img)->save($destinationPath1.'/'.$file_name1);
                $category->BANNER = $destinationPath1 .'/'. $file_name1;
            }
            $category->save();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create Blog Category !', 'web.blog.category.edit');
        }
        DB::commit();
        return $this->formatResponse(true, 'Blog Category has been created successfully !', 'web.blog.category',$category->PK_NO);
    }

    public function getDelete(int $id)
    {
        DB::begintransaction();
        try {
            $product = BlogCategory::find($id)->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete Blog Category !', 'web.blog.category');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully Blog Category Deleted !', 'web.blog.category');
    }

}
