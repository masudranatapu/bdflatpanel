<?php

namespace App\Http\Controllers\Web;
use Auth;
use App\Models\Web\About;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Http\Requests\Web\ArticleRequest;

class AboutController extends Controller
{

    protected $about;

    public function __construct(About $about)
    {
        $this->about     = $about;
    }

    public function getIndex(Request $request){
        $this->resp = $this->about->getAbout($request);
        $data['about'] = $this->resp->data;
        return view('admin.web.about.index')->withData($data);
    }


    public function postStore(Request $request)
    {
        $this->resp = $this->about->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postEditorImageUpload(Request $request)
    {
        if (!is_null($request->file('image')))
        {
        $image = $request->file('image');
        $extension = $image->getClientOriginalExtension();
        $file_path = 'uploads/' . date("Y/m") . '/photos';
        $base_name = preg_replace('/\..+$/', '', $image->getClientOriginalName());
        $base_name = explode(' ', $base_name);
        $base_name = implode('-', $base_name);
        $img = Image::make($image->getRealPath());
        $feature_image = $base_name . "-" . uniqid().'.webp';
        Image::make($img)->save($file_path.'/'.$feature_image);
        $image_name = $file_path .'/'. $feature_image;
            return   url('/').'/'.$image_name;
        }
    }


}
