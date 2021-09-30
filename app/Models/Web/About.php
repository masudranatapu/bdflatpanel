<?php
namespace App\Models\Web;
use Illuminate\Support\Str;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    use RepoResponse;
    protected $table        = 'WEB_ABOUT';
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

    public function getAbout()
    {
        $data =  About::first();
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.web.about.index', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.web.about.index', null);
    }

    public function postStore($request)
    {
        //dd($request->all());
        DB::beginTransaction();
        try {

            if(!empty($request->id)){

                $about                 = About::findOrFail($request->id);
                $about->TITLE          = $request->title;
                $about->SUB_TITLE      = $request->subtitle;
                if ($request->hasFile('banner'))
                {
                $image                 = $request->file('banner');
                $extension             = $image->getClientOriginalExtension();
                $feature_path          = 'uploads/' . date("Y/m") . '/photos';
                $base_name             = preg_replace('/\..+$/', '', $image->getClientOriginalName());
                $base_name             = explode(' ', $base_name);
                $base_name             = implode('-', $base_name);
                if (!file_exists($feature_path)) {
                    mkdir($feature_path, 0755, true);
                }
                $img                    = Image::make($image->getRealPath());
                $feature_image          = $base_name . "-" . uniqid().'.webp';
                Image::make($img)->save($feature_path.'/'.$feature_image);
                $about->BANNER          = $feature_path .'/'. $feature_image;
                }
                $about->VISION_TITLE    = $request->vision_title;
                $about->VISION_DESCRIPTION = $request->vision_description;
                $about->MISSION_TITLE   = $request->mission_title;
                $about->MISSION_DESCRIPTION = $request->mission_description;
                $about->INTRO_TITLE     = $request->intro_title;
                $about->INTRO_SUBTITLE  = $request->intro_subtitle;
                $about->INTRO_DESCRIPTION = $request->intro_description;
                $about->IS_ACTIVE       = $request->is_active;
                if ($request->hasFile('intro_image_1'))
                {
                $image                  = $request->file('intro_image_1');
                $extension              = $image->getClientOriginalExtension();
                $feature_path           = 'uploads/' . date("Y/m") . '/photos';
                $base_name              = preg_replace('/\..+$/', '', $image->getClientOriginalName());
                $base_name              = explode(' ', $base_name);
                $base_name              = implode('-', $base_name);
                if (!file_exists($feature_path)) {
                    mkdir($feature_path, 0755, true);
                }
                $img                    = Image::make($image->getRealPath());
                $feature_image          = $base_name . "-" . uniqid().'.webp';
                Image::make($img)->save($feature_path.'/'.$feature_image);
                $about->INTRO_IMG_1      = $feature_path .'/'. $feature_image;
                }
                if ($request->hasFile('intro_image_2'))
                {
                $image              = $request->file('intro_image_2');
                $extension          = $image->getClientOriginalExtension();
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
                $about->INTRO_IMG_2      = $feature_path .'/'. $feature_image;
                }
                $about->save();
            }
            else{

                $about                  = new About();
                $about->TITLE                                = $request->title;
                $about->SUB_TITLE                              = $request->subtitle;
                if ($request->hasFile('banner'))
                {
                $image              = $request->file('banner');
                $extension          = $image->getClientOriginalExtension();
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
                $about->BANNER      = $feature_path .'/'. $feature_image;
                }
                $about->VISION_TITLE                     = $request->vision_title;
                $about->VISION_DESCRIPTION                          = $request->vision_description;
                $about->MISSION_TITLE                                 = $request->mission_title;
                $about->MISSION_DESCRIPTION                           = $request->mission_description;
                $about->INTRO_TITLE                           = $request->intro_title;
                $about->INTRO_SUBTITLE                           = $request->intro_subtitle;
                $about->INTRO_DESCRIPTION                           = $request->intro_description;
                $about->IS_ACTIVE                            = $request->is_active;
                if ($request->hasFile('intro_image_1'))
                {
                $image              = $request->file('intro_image_1');
                $extension          = $image->getClientOriginalExtension();
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
                $about->INTRO_IMG_1      = $feature_path .'/'. $feature_image;
                }

                if ($request->hasFile('intro_image_2'))
                {
                $image              = $request->file('intro_image_2');
                $extension          = $image->getClientOriginalExtension();
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
                $about->INTRO_IMG_2      = $feature_path .'/'. $feature_image;
                }
                $about->save();


            }

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create article !', 'web.about.create');
        }
        DB::commit();
        return $this->formatResponse(true, 'article has been created successfully !', 'web.about',$about->PK_NO);
    }


}
