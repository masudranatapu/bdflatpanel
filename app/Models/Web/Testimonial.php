<?php
namespace App\Models\Web;
use Illuminate\Support\Str;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use RepoResponse;
    protected $table        = 'WEB_TESTIMONIALS';
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

    public function getTestimonials()
    {
        $data =  Testimonial::get();
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.web.testimonial.index', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.web.testimonial.index', null);
    }

    public function getTestimonial(int $id)
    {
        $data =  Testimonial::findOrFail($id);
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.web.testimonial.index', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.web.testimonial.index', null);
    }

    public function postStore($request)
    {
        //dd($request->all());
        DB::beginTransaction();
        try {
                $about                  = new Testimonial();
                $about->NAME            = $request->name;
                $about->DESIGNATION     = $request->designation;
                $about->DESCRIPTION     = $request->description;

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
                $about->IMAGE      = $feature_path .'/'. $feature_image;
                }
                $about->IS_ACTIVE                            = $request->is_active;

                $about->save();

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create Testimonial !', 'web.testimonial');
        }
        DB::commit();
        return $this->formatResponse(true, 'Testimonial has been created successfully !', 'web.testimonial');
    }

    public function postUpdate($request, int $id)
    {
        //dd($request->all());
        DB::beginTransaction();
        try {
                $about                 = Testimonial::findOrFail($id);
                $about->NAME          = $request->name;
                $about->DESIGNATION      = $request->designation;
                $about->DESCRIPTION    = $request->description;

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
                $about->IMAGE          = $feature_path .'/'. $feature_image;
                }
                $about->IS_ACTIVE       = $request->is_active;
                $about->update();

        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to update Testimonial !', 'web.testimonial');
        }
        DB::commit();
        return $this->formatResponse(true, 'Testimonial has been update successfully !', 'web.testimonial');
    }


    public function getDelete(int $id)
    {
        DB::begintransaction();
        try {
            $product = Testimonial::find($id)->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete Testimonial !', 'web.testimonial');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully Testimonial Deleted !', 'web.testimonial');
    }


}
