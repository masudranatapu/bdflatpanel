<?php
namespace App\Models\Web;
use Illuminate\Support\Str;
use App\Traits\RepoResponse;
use App\Models\Web\BlogCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use RepoResponse;
    protected $table        = 'WEB_ARTICLE';
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
        return $this->formatResponse(true, '', 'web.blog.article', $data);
    }
    public function getShow(int $id)
    {
        $data =  Article::find($id);
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.article.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.article.list', null);
    }

    public function postStore($request)
    {
        DB::beginTransaction();
        try {
            $article                                       = new Article();
            $article->TITLE                                = $request->title;
            $str                                           = strtolower($request->title);
            $article->URL_SLUG                             = Str::slug($str);
            $article->SUMMARY                              = $request->summary;
            $article->BODY                                 = $request->body;
            $article->ARTICLE_CATEGORY                     = $request->category;
            if(!empty($request->author)){
                $article->AUTHOR_NAME                          = $request->author;
            }
            else
            {

                $article->AUTHOR_NAME                       =NULL;
            }
            $article->AUTHOR_NAME                          = $request->author;
            $article->TAGS                                 = $request->tags;
            $article->IS_FEATURE                           = $request->is_feature;
            $article->IS_ACTIVE                            = $request->is_active;
            $article->ORDER_ID                             = Article::max('ORDER_ID')+1;
            $article->META_KEYWARDS                        = $request->meta_keywards;
            $article->META_DESCRIPTION                     = $request->meta_description;
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
            if (!file_exists($thumb_path)) {
                mkdir($thumb_path, 0755, true);
            }
            $img                = Image::make($image->getRealPath());
            $feature_image      = $base_name . "-" . uniqid().'.webp';
            $thumb_image        = $base_name . "-" . uniqid(). '.webp' ;
            Image::make($img)->save($feature_path.'/'.$feature_image);
            Image::make($img)->encode('webp', 100)->resize(400, null, function ($constraint) {
                        $constraint->aspectRatio();
                    // $constraint->upsize();
                    })->save($thumb_path.'/'.$thumb_image);
            $article->FEATURE_IMAGE      = $feature_path .'/'. $feature_image;
            $article->THUMBNAIL_IMAGE    = $thumb_path .'/'. $thumb_image;
            }
            $article->save();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create article !', 'web.blog.article.create');
        }
        DB::commit();
        return $this->formatResponse(true, 'article has been created successfully !', 'web.blog.article',$article->PK_NO);
    }

     public function postUpdate($request)
    {
        //dd($request->all());
        DB::beginTransaction();
        try {
            $article                                       = Article::findOrFail($request->id);
            $article->TITLE                                = $request->title;
            $str                                           = strtolower($request->title);
            $article->URL_SLUG                             = Str::slug($str);
            $article->SUMMARY                              = $request->summary;
            $article->BODY                                 = $request->body;
            $article->ARTICLE_CATEGORY                     = $request->category;
            $article->TAGS                                 = $request->tags;
            $article->IS_FEATURE                           = $request->is_feature;
            if(!empty($request->author)){
                $article->AUTHOR_NAME                      = $request->author;
            }
            else
            {
                $article->AUTHOR_NAME                      =NULL;
            }
            $article->IS_ACTIVE                            = $request->is_active;
            //$article->ORDER_ID                             = Article::max('ORDER_ID')+1;
            $article->META_KEYWARDS                        = $request->meta_keywards;
            $article->META_DESCRIPTION                     = $request->meta_description;
            if ($request->hasFile('feature_image'))
            {
            $image          = $request->file('feature_image');
            $extension      = $image->getClientOriginalExtension();
            $feature_path   = 'uploads/' . date("Y/m") . '/photos';
            $thumb_path     = 'uploads/' . date("Y/m") . '/photos/thumb';
            $base_name      = preg_replace('/\..+$/', '', $image->getClientOriginalName());
            $base_name      = explode(' ', $base_name);
            $base_name      = implode('-', $base_name);
            if (!file_exists($feature_path)) {
                mkdir($feature_path, 0755, true);
            }
            if (!file_exists($thumb_path)) {
                mkdir($thumb_path, 0755, true);
            }
            $img            = Image::make($image->getRealPath());
            $feature_image  = $base_name . "-" . uniqid().'.webp';
            $thumb_image    = $base_name . "-" . uniqid(). '.webp' ;
            Image::make($img)->save($feature_path.'/'.$feature_image);
            Image::make($img)->encode('webp', 100)->resize(400, null, function ($constraint) {
                        $constraint->aspectRatio();
                    // $constraint->upsize();
                    })->save($thumb_path.'/'.$thumb_image);
            $article->FEATURE_IMAGE      = $feature_path .'/'. $feature_image;
            $article->THUMBNAIL_IMAGE    = $thumb_path .'/'. $thumb_image;
            }
            $article->save();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create article !', 'web.blog.article.edit',$request->id);
        }
        DB::commit();
        return $this->formatResponse(true, 'article has been created successfully !', 'web.blog.article',$article->PK_NO);
    }

    public function textEditorImageUpload(Request $request)
    {
        if (!is_null($request->file('image')))
        {

        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension();
        $destinationPath1 = 'uploads/' . date("Y/m") . '/photos';
        $base_name = preg_replace('/\..+$/', '', $image->getClientOriginalName());
        $base_name = explode(' ', $base_name);
        $base_name = implode('-', $base_name);
        $img = Image::make($image->getRealPath());
        $feature_image = $base_name . "-" . uniqid().'.webp';
        Image::make($img)->save('../'.$destinationPath1.'/'.$feature_image);
        $image_name = $destinationPath1 .'/'. $feature_image;
            return $image_name;
             //url('../') . '/' . $file_path . '/' . $image_name;
        }
    }


    public function getDelete(int $id)
    {
        DB::begintransaction();
        try {
            $article = Article::find($id)->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete product !', 'web.blog.article');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully delete Article !', 'web.blog.article');
    }

}
