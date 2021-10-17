<?php
namespace App\Repositories\Admin\Slider;
use App\Models\Web\Slider;
use App\Traits\RepoResponse;
use DB;
use Image;
use Auth;
class SliderAbstract implements SliderInterface
{
    use RepoResponse;

    protected $slider;

    public function __construct(Slider $slider)
    {
        $this->slider     = $slider;
    }

    public function getPaginatedList($request, int $per_page = 20)
    {
        $data = $this->slider->where('IS_ACTIVE',1)->orderBy('ORDER_BY', 'DESC')->get();
        return $this->formatResponse(true, '', 'web.home.slider', $data);
    }


    public function postStore($request)
    {
        DB::beginTransaction();
        try {
            $slider                  = new Slider();
            $slider->TITLE           = $request->title;
            $slider->SUBTITLE        = $request->subtitle;
            $slider->URL_LINK        = $request->url_link;
            $slider->IS_FEATURE      = $request->is_feature;
            $slider->IS_ACTIVE       = $request->is_active;
            $slider->CREATED_BY      = Auth::user()->PK_NO;
            $slider->ORDER_BY        = Slider::max('ORDER_BY')+1;
            $slider->CREATED_ON      = date("Y-m-d h:i:s", time());
            $image                   = $this->uploadImage($request->banner);
            $image2                  = $this->uploadImage($request->mobile_banner);
            $slider->BANNER          = '/'.$image['image_url'];
            $slider->IMAGE_NAME      = $image['image_name'];
            $slider->MOBILE_BANNER   = '/'.$image2['image_url'];
            $slider->MOBILE_IMAGE_NAME= $image2['image_name'];
            $slider->save();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, $e->getMessage(), 'admin.web.slider.create');
        }
        DB::commit();

        return $this->formatResponse(true, 'Slider has been created successfully !', 'web.home.slider');
    }


    public function uploadImage($image)
    {

      if($image)
      {
          $filename = $image->getClientOriginalExtension();

          $destinationPath1 = 'media/images/banner';

          if (!file_exists($destinationPath1)) {

              mkdir($destinationPath1, 0755, true);
          }

           $img = Image::make($image->getRealPath());

          $file_name1 = 'prod_'. date('dmY'). '_' .uniqid().'.'.$filename;

            Image::make($img)->save($destinationPath1.'/'.$file_name1);

          $imageUrl1 = $destinationPath1 .'/'. $file_name1;

      }

      return $data = [
          'image_name' => $file_name1,
          'image_url' => $imageUrl1

      ];

    }



    public function postUpdate($request, $id)
    {

        DB::beginTransaction();

        try {

            $slider                  = Slider::find($id);
            $slider->TITLE           = $request->title;
            $slider->SUBTITLE        = $request->subtitle;
            $slider->URL_LINK        = $request->url_link;
            $slider->IS_FEATURE      = $request->is_feature;
            $slider->IS_ACTIVE       = $request->is_active;
            $slider->MODIFIED_BY     = Auth::user()->PK_NO;
            $slider->MODIFIED_ON     = date("Y-m-d h:i:s", time());

            if(!empty($request->banner)){
                $image               = $this->uploadImage($request->banner);
                $slider->BANNER      = '/'.$image['image_url'];
                $slider->IMAGE_NAME  = $image['image_name'];
            }

            if(!empty($request->mobile_banner)){
                $image2                  = $this->uploadImage($request->mobile_banner);
                $slider->MOBILE_BANNER   = '/'.$image2['image_url'];
                $slider->MOBILE_IMAGE_NAME= $image2['image_name'];
            }

            $slider->update();
        } catch (\Exception $e) {
            DB::rollback();

            return $this->formatResponse(false, 'Unable to update Slider !', 'web.home.slider');
        }

        DB::commit();

        return $this->formatResponse(true, 'Slider has been updated successfully !', 'web.home.slider');
    }


    public function getShow(int $id)
    {
        $data =  Slider::find($id);

        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'web.home.slider.edit', $data);
        }

        return $this->formatResponse(false, 'Did not found data !', 'web.home.slider', null);
    }

    public function delete($id)
    {
        DB::begintransaction();
        try {
            $category = Slider::find($id)->delete();

        } catch (\Exception $e) {

            DB::rollback();
            return $this->formatResponse(false, 'Unable to delete this Slider !', 'web.home.slider');
        }

        DB::commit();


        return $this->formatResponse(true, 'Successfully delete this Slider !', 'web.home.slider');
    }



}
