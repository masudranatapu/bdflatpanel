<?php

namespace App\Http\Controllers\Web;
use App\Models\Web\AboutUs;
use App\Models\Web\TeamMember;
use App\Models\Web\Testimonial;
use Auth;
use App\Models\Web\About;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Http\Requests\Web\ArticleRequest;

class TeamMemberController extends Controller
{

    protected $team_members;

    public function __construct(TeamMember $team_members)
    {
        $this->team_members     = $team_members;
    }

    public function getIndex(Request $request){
        $this->resp = $this->team_members->getTestimonials($request);
        $data['team_members'] = $this->resp->data;
        return view('admin.web.team-member.index')->withData($data);
    }

    public function getCreate(){
        return view('admin.web.team-member.create');
    }

    public function getEdit($id){
        $this->resp = $this->team_members->getTestimonial($id);
        $data['team_members'] = $this->resp->data;
        return view('admin.web.team-member.edit')->withData($data);
    }


    public function postStore(Request $request)
    {
        $this->resp = $this->team_members->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function postUpdate(Request $request, $id)
    {
        $this->resp = $this->team_members->postUpdate($request, $id);
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

    public function getDelete($id)
    {
        $this->resp = $this->team_members->getDelete($id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }


}
