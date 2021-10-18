<?php
namespace App\Repositories\Admin\Ads;

use App\Models\Web\AdsImages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Web\Ads;
use App\Models\Web\AdsPosition;
use App\Traits\RepoResponse;

class AdsAbstract implements AdsInterface
{
    use RepoResponse;

    protected $ads;
    protected $adsPosition;
    protected $adsImages;

    public function __construct(Ads $ads, AdsPosition $adsPosition, AdsImages $adsImages)
    {
        $this->ads = $ads;
        $this->adsPosition = $adsPosition;
        $this->adsImages = $adsImages;
    }

    public function getPaginatedList($request): object
    {
        $data = $this->ads->with(['position', 'images'])->orderBy('PK_NO', 'ASC')->get();
        return $this->formatResponse(true, '', 'web.ads', $data);
    }

    public function storeAd($request): object
    {
        $status = false;
        $msg = 'Ad could not be added!';

        DB::beginTransaction();
        try {
            $ad = new Ads();
            $ad->F_AD_POSITION_NO = $request->position;
            $ad->AVAILABLE_TO = date('Y-m-d', strtotime($request->end_date));
            $ad->AVAILABLE_FROM = date('Y-m-d', strtotime($request->start_date));
            $ad->STATUS = $request->status;
            $ad->save();

            $status = true;
            $msg = 'Add added successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($status, $msg, 'web.ads');
    }

    public function editAd($id): object
    {
        $data['positions'] = $this->adsPosition->orderBy('PK_NO', 'ASC')->pluck('NAME', 'POSITION_ID');
        $data['ad'] = $this->ads->find($id);
        return $this->formatResponse(true, '', 'web.ads', $data);
    }

    public function updateAd($request, $id): object
    {
        $status = false;
        $msg = 'Ad could not be updated!';

        DB::beginTransaction();
        try {
            $ad = Ads::find($id);
            $ad->F_AD_POSITION_NO = $request->position;
            $ad->AVAILABLE_TO = date('Y-m-d', strtotime($request->end_date));
            $ad->AVAILABLE_FROM = date('Y-m-d', strtotime($request->start_date));
            $ad->STATUS = $request->status;
            $ad->save();

            $status = true;
            $msg = 'Add updated successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($status, $msg, 'web.ads');
    }

    public function getAdsPositions($request): object
    {
        $data = $this->adsPosition->orderBy('PK_NO', 'ASC')->get();
        return $this->formatResponse(true, '', 'web.ads_position', $data);
    }

    public function getAdsPosition(int $id)
    {
        return AdsPosition::find($id);
    }

    public function storeAdsPosition($request): object
    {
        $status = false;
        $msg = 'Could not add ads position!';

        DB::beginTransaction();
        try {
            $adsPosition = new AdsPosition();
            $adsPosition->NAME = $request->name;
            $adsPosition->POSITION_ID = $request->position;
            $adsPosition->IS_ACTIVE = $request->status;
            $adsPosition->save();

            $status = true;
            $msg = 'Ads position added';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($status, $msg, 'web.ads_position');
    }

    public function updateAdsPosition($request, $id): object
    {
        $status = false;
        $msg = 'Could not update ads position!';

        DB::beginTransaction();
        try {
            $adsPosition = $this->getAdsPosition($id);
            $adsPosition->NAME = $request->name;
            $adsPosition->POSITION_ID = $request->position;
            $adsPosition->IS_ACTIVE = $request->status;
            $adsPosition->save();

            $status = true;
            $msg = 'Ads position updated';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($status, $msg, 'web.ads_position');
    }

    public function getAdsImages($id): object
    {
        $data['images'] = $this->adsImages->where('F_ADS_NO', $id)->orderByDesc('ORDER_ID')->get();
        $data['id'] = $id;
        return $this->formatResponse(true, '', 'web.ads.images', $data);
    }

    public function storeAdsImages($request, $id)
    {
        $status = false;
        $msg = 'Image could not be added!';

        DB::beginTransaction();
        try {
            $adImg = new AdsImages();
            $adImg->F_ADS_NO = $id;
            $adImg->ORDER_ID = $request->order_id;
            $adImg->URL = $request->url;

            $image = $request->file('images')[0];
            $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = '/uploads/ads/' . $id . '/';
            $image->move(public_path($imagePath), $imageName);

            $adImg->IMAGE_PATH = $imagePath . $imageName;
            $adImg->save();

            $status = true;
            $msg = 'Image added successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($status, $msg, 'web.ads.image');
    }

    public function updateAdsImage($request): object
    {
        $status = false;
        $msg = 'Image order could not be update!';

        DB::beginTransaction();
        try {
            $adImg = AdsImages::find($request->id);
            $adImg->ORDER_ID = $request->order_id;
            $adImg->URL = $request->url;
            $adImg->save();

            $status = true;
            $msg = 'Image order updated successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($status, $msg, 'web.ads.image');
    }

    public function deleteAdsImage(int $id): object
    {
        $status = false;
        $msg = 'Image could not be deleted!';

        DB::beginTransaction();
        try {
            $adImg = AdsImages::find($id);

            $imageFile = $adImg->IMAGE_PATH;
            $adImg->delete();
            unlink(public_path($imageFile));

            $status = true;
            $msg = 'Image deleted successfully!';
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
        }

        DB::commit();
        return $this->formatResponse($status, $msg, 'web.ads.image');
    }
}
