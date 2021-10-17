<?php

namespace App\Repositories\Api\Dispatch;

interface DispatchInterface
{
    public function postCodRtcDispatchList($request);
    public function postCodRtcDispatchTransfer($request);
    public function postCodRtcDispatchItem($request);
    public function postRtsDispatchList($request);
    public function postRtsDispatchedItemList($request);
    public function postRtsDispatchedList($request);
    public function postCOnsignmentList($request);
    public function postRtsBatchList($request);
    public function postDispatch($request);
    public function postProductOfTrackingNo($request);
    public function postCodRtsZone($request);
    public function postCodRtcOrderList($request);
    public function postCodRtcDispatch($request);
    public function postCodRtcAcknowledge($request);
    public function postCodRtcBoxItemTransfer($request);
}
