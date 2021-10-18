<?php

namespace App\Repositories\Admin\BankState;

interface BankStateInterface
{
    public function getPaginatedList($request, int $per_page = 5);
    public function postStore($request);
    // public function postUpdate($request, int $id);
    public function postVerify($request);
    public function getUnVerify($id);
    public function postDraftToSave($request);
    public function postMarkAsUsed($request);
    public function delete($id);
    public function postDeleteBulk($request);
}
