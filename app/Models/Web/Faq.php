<?php
namespace App\Models\Web;
use Illuminate\Support\Str;
use App\Traits\RepoResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use RepoResponse;
    protected $table        = 'WEB_FAQ';
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
        return $this->formatResponse(true, '', 'web.faq', $data);
    }
    public function getShow(int $id)
    {
        $data =  Faq::find($id);
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'web.faq.edit', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'web.faq', null);
    }

    public function postStore($request)
    {
        DB::beginTransaction();
        try {
            $faq                                       = new Faq();
            $faq->QUESTION                             = $request->question;
            $faq->ANSWER                               = $request->answer;
            $faq->IS_ACTIVE                            = $request->is_active;
            $faq->ORDER_ID                             = Faq::max('ORDER_ID')+1;
            $faq->save();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create faq !', 'web.faq.create');
        }
        DB::commit();
        return $this->formatResponse(true, 'faq has been created successfully !', 'web.faq',$faq->PK_NO);
    }

     public function postUpdate($request)
    {
       // dd($request->all());
        DB::beginTransaction();
        try {
            $faq                                       = Faq::findOrFail($request->id);
            $faq->QUESTION                             = $request->question;
            $faq->ANSWER                               = $request->answer;
            $faq->IS_ACTIVE                            = $request->is_active;
            $faq->save();
        } catch (\Exception $e) {
            dd($e);
            DB::rollback();
            return $this->formatResponse(false, 'Unable to create article !', 'web.faq.edit',$request->id);
        }
        DB::commit();
        return $this->formatResponse(true, 'article has been created successfully !', 'web.faq',$faq->PK_NO);
    }

    public function getDelete(int $id)
    {
        DB::begintransaction();
        try {
            $faq = Faq::find($id)->delete();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete product !', 'web.faq');
        }
        DB::commit();
        return $this->formatResponse(true, 'Successfully delete Article !', 'web.faq');
    }

}
