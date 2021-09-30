<?php

namespace App\Repositories\Admin\Dispatch;

interface DispatchInterface
{
    public function getPaginatedList($request, int $per_page = 50);
    public function postStore($request);
    public function postMarkPickup($request);
    public function postAssignOrderBulkItem($request);
    public function postSpecialNoteStatus($request);
    public function postAssignOrderItem($request);
    public function getCodRtcUserStockList($request,$id);
    public function getAppPendingDispatchList();
    // public function delete($id);
    // public function getDueOrdersCustomer(int $id);
    // public function getDueOrdersReseller(int $id);
    // public function findOrThrowException($id);
    // public function ajaxDelete($id);
    // public function ajaxPayment($request);
    // public function updateBooktoOrder($request,$id);
    // public function getIndex();
    public function getBatchCollectList();
    public function getBatchCollectedList();
    public function getOrderForDisopatch($id);
    public function getCollectionBreakdown($id);
    public function getRevertDispatch($id);
    public function getRevertbatch($id);
    // public function getCustomerAddress($id,$pk_no);
}
