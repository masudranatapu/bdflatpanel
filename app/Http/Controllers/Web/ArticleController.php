<?php

namespace App\Http\Controllers\Web;
use Auth;
use App\Models\Web\Article;
use Illuminate\Http\Request;
use App\Models\Web\BlogCategory;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Http\Requests\Web\ArticleRequest;

class ArticleController extends Controller
{

    protected $article;

    public function __construct(Article $article)
    {
        $this->article     = $article;
    }

    public function getAllArticle(Request $request){
        $this->resp = $this->article->getPaginatedList($request);
        $data['article'] = $this->resp->data;
        return view('admin.web.article.index')->withData($data);
    }

     public function getCreate(){
         $data['category'] = BlogCategory::pluck('NAME','PK_NO');
        return view('admin.web.article.create')->withData($data);
    }

    public function postStore(ArticleRequest $request)
    {
        $this->resp = $this->article->postStore($request);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    public function getEdit(Request $request, $id)
    {
        $data[] = '';
        $this->resp = $this->article->getShow($id);
        if (!$this->resp->status) {
            return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
        }
        $data['article'] = $this->resp->data;
        $data['category'] = BlogCategory::pluck('NAME','PK_NO');
        return view('admin.web.article.edit')->withData($data);
    }

    public function postUpdate(ArticleRequest $request, $id)
    {
        $this->resp = $this->article->postUpdate($request, $id);
        if (!$this->resp->status) {
            return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
        }
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }

    // public function postEditorImageUpload(Request $request)
    // {
    //     $this->resp = $this->article->postUpdate($request, $id);
    //     if (!$this->resp->status) {
    //         return redirect()->back()->with($this->resp->redirect_class, $this->resp->msg);
    //     }
    //     return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    // }

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
        $this->resp = $this->article->getDelete($id);
        return redirect()->route($this->resp->redirect_to)->with($this->resp->redirect_class, $this->resp->msg);
    }
    public function changeActiveStatus(Request $request){

        $id                     = $request->id;
        $article                = Article::findOrFail($id);
        $article->IS_ACTIVE     = !$article->IS_ACTIVE;
        $article->MODIFIED_BY   = Auth::user()->PK_NO;
        $article->update();
        return response()->json($article);
    }

    public function changeFeatureStatus(Request $request){

        $id                     = $request->id;
        $article                = Article::findOrFail($id);
        $article->IS_FEATURE    = !$article->IS_FEATURE;
        $article->MODIFIED_BY   = Auth::user()->PK_NO;
        $article->update();
        return response()->json($article);
    }

}
