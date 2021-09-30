<?php
namespace App\Models\Web;
use Illuminate\Support\Str;
use App\Traits\RepoResponse;
use App\Models\Web\BlogCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use RepoResponse;
    protected $table        = 'WEB_PAGES';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;
    const CREATED_AT        = 'SS_CREATED_ON';
    const UPDATED_AT        = 'SS_MODIFIED_ON';

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
           $user = Auth::user();
           $model->F_SS_CREATED_BY = $user->PK_NO;
        });

        static::updating(function($model)
        {
           $user = Auth::user();
           $model->F_SS_MODIFIED_BY = $user->PK_NO;
        });
    }
    public function getPaginatedList($request, int $per_page = 2000)
    {
        $data = $this->get();

        return $this->formatResponse(true, '', 'web.page', $data);
    }
    public function getShow(int $id)
    {
        $data =  Page::find($id);
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'web.page.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'web.page', null);
    }

    public function postStore($request)
    {

       // dd($request->all());
        DB::beginTransaction();
        try {
            $page                                       = new Page();
            $page->TITLE                                = $request->title;
            $str                                        = strtolower($request->title);
            $page->URL_SLUG                             = Str::slug($str);
            $page->SUB_TITLE                            = $request->subtitle;
            $page->BODY                                 = $request->body;
            $page->IS_ACTIVE                            = $request->is_active;
            $page->ORDER_ID                             = Page::max('ORDER_ID')+1;
            $page->META_KEYWARDS                        = $request->meta_keywards;
            $page->META_DESCRIPTION                     = $request->meta_description;
            if ($request->hasFile('feature_image'))
            {
            $image              = $request->file('feature_image');
            $feature_path       = 'uploads/' . date("Y/m") . '/photos';
            $base_name          = preg_replace('/\..+$/', '', $image->getClientOriginalName());
            $base_name          = explode(' ', $base_name);
            $base_name          = implode('-', $base_name);
            if (!file_exists($feature_path)) {
                mkdir($feature_path, 0755, true);
            }
            $img                = Image::make($image->getRealPath());
            $feature_image      = $base_name . "-" . uniqid().'.webp';
            Image::make($img)->save($feature_path.'/'.$feature_image);
            $page->FEATURE_IMAGE      = $feature_path .'/'. $feature_image;
            }

            if ($request->hasFile('banner_image'))
            {
            $image              = $request->file('banner_image');
            $feature_path       = 'uploads/' . date("Y/m") . '/photos';
            $base_name          = preg_replace('/\..+$/', '', $image->getClientOriginalName());
            $base_name          = explode(' ', $base_name);
            $base_name          = implode('-', $base_name);
            if (!file_exists($feature_path)) {
                mkdir($feature_path, 0755, true);
            }
            $img                = Image::make($image->getRealPath());
            $banner_image      = $base_name . "-" . uniqid().'.webp';
            Image::make($img)->save($feature_path.'/'.$banner_image);
            $page->BANNER      = $feature_path .'/'. $banner_image;
            }
            $page->save();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create page !', 'web.page.create');
        }
        DB::commit();
        return $this->formatResponse(true, 'page has been created successfully !', 'web.page',$page->PK_NO);
    }

     public function postUpdate($request)
    {
       // dd($request->all());
        DB::beginTransaction();
        try {
            $page                                       = Page::findOrFail($request->id);
            $page->TITLE                                = $request->title;
            $str                                        = strtolower($request->title);
            $page->URL_SLUG                             = Str::slug($str);
            $page->SUB_TITLE                            = $request->subtitle;
            $page->BODY                                 = $request->body;
            $page->IS_ACTIVE                            = $request->is_active;
            $page->ORDER_ID                             = Page::max('ORDER_ID')+1;
            $page->META_KEYWARDS                        = $request->meta_keywards;
            $page->META_DESCRIPTION                     = $request->meta_description;
            if ($request->hasFile('feature_image'))
            {
            $image              = $request->file('feature_image');
            $extension          = $image->getClientOriginalExtension();
            $feature_path       = 'uploads/' . date("Y/m") . '/photos';
            $thumb_path         = 'uploads/' . date("Y/m") . '/photos/thumb';
            $base_name          = preg_replace('/\..+$/', '', $image->getClientOriginalName());
            $base_name          = explode(' ', $base_name);
            $base_name          = implode('-', $base_name);
            if (!file_exists($feature_path)) {
                mkdir($feature_path, 0755, true);
            }
            $img                = Image::make($image->getRealPath());
            $feature_image      = $base_name . "-" . uniqid().'.webp';
            Image::make($img)->save($feature_path.'/'.$feature_image);
            $page->FEATURE_IMAGE      = $feature_path .'/'. $feature_image;
            }

            if ($request->hasFile('banner_image'))
            {
            $image              = $request->file('banner_image');
            $extension          = $image->getClientOriginalExtension();
            $feature_path       = 'uploads/' . date("Y/m") . '/photos';
            $thumb_path         = 'uploads/' . date("Y/m") . '/photos/thumb';
            $base_name          = preg_replace('/\..+$/', '', $image->getClientOriginalName());
            $base_name          = explode(' ', $base_name);
            $base_name          = implode('-', $base_name);
            if (!file_exists($feature_path)) {
                mkdir($feature_path, 0755, true);
            }
            $img                = Image::make($image->getRealPath());
            $banner_image      = $base_name . "-" . uniqid().'.webp';
            Image::make($img)->save($feature_path.'/'.$banner_image);
            $page->BANNER      = $feature_path .'/'. $banner_image;
            }
            $page->save();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create page !', 'web.page.edit',$request->id);
        }
        DB::commit();
        return $this->formatResponse(true, 'page has been created successfully !', 'web.page',$page->PK_NO);
    }

    public function getDelete(int $id)
    {
        DB::begintransaction();
        try {
            $page = Page::find($id)->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete product !', 'web.page');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully delete page !', 'web.page');
    }

}
