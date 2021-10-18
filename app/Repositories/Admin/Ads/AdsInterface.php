<?php

namespace App\Repositories\Admin\Ads;

interface AdsInterface
{
    public function getPaginatedList($request);

    public function storeAd($request);

    public function editAd(int $id);

    public function updateAd($request, int $id);

    public function getAdsPositions($request);

    public function getAdsPosition(int $id);

    public function storeAdsPosition($request);

    public function updateAdsPosition($request, int $id);

    public function getAdsImages($id);

    public function storeAdsImages($request, int $id);

    public function updateAdsImage($request);

    public function deleteAdsImage(int $id);
}
