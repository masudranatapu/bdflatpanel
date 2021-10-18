<?php
namespace App\Models\Web;
use Illuminate\Support\Str;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    use RepoResponse;
    protected $table        = 'WEB_TEAM_MEMBERS';
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
        $data =  TeamMember::get();
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.web.team_members.index', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.web.team_members.index', null);
    }

    public function getTestimonial(int $id)
    {
        $data =  TeamMember::findOrFail($id);
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.web.team_members.index', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.web.team_members.index', null);
    }

    public function postStore($request)
    {
        //dd($request->all());
        DB::beginTransaction();
        try {
                $about                  = new TeamMember();
                $about->NAME            = $request->name;
                $about->DESIGNATION     = $request->designation;
                $about->FB_URL          = $request->fb_url;
                $about->TWITTER_URL     = $request->twitter_url;
                $about->LINKEDIN_URL    = $request->linkedin_url;
                $about->PRINTEREST_URL  = $request->printerest_url;

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
            return $this->formatResponse(false, 'Unable to create Team Member !', 'web.team_members');
        }
        DB::commit();
        return $this->formatResponse(true, 'Team Member has been created successfully !', 'web.team_members');
    }

    public function postUpdate($request, int $id)
    {
        //dd($request->all());
        DB::beginTransaction();
        try {
                $about                 = TeamMember::findOrFail($id);
                $about->NAME          = $request->name;
                $about->DESIGNATION      = $request->designation;
                $about->FB_URL          = $request->fb_url;
                $about->TWITTER_URL     = $request->twitter_url;
                $about->LINKEDIN_URL    = $request->linkedin_url;
                $about->PRINTEREST_URL  = $request->printerest_url;

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
            return $this->formatResponse(false, 'Unable to update Team Member !', 'web.team_members');
        }
        DB::commit();
        return $this->formatResponse(true, 'Team Member has been update successfully !', 'web.team_members');
    }


    public function getDelete(int $id)
    {
        DB::begintransaction();
        try {
            $product = TeamMember::find($id)->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete Team Member !', 'web.team_members');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully Team Member Deleted !', 'web.team_members');
    }


}
