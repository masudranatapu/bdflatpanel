<?php

namespace App\Repositories\Api\Boxing;

interface BoxingInterface
{
    public function getProductBox($request);
    public function getRebox($request);
    public function getUnboxList($request);
    public function getUnbox($request);
    public function postBoxList($request);
    public function getPriorityUnboxList($request);
    public function postBoxLabelExists($request);
    public function postBoxListDetails($request);
    public function postYetToBox($request);
    public function postUnboxListItem($request);
    public function postBoxLabelUpdate($request);
    public function priorityUnboxListItem($request);
    public function getUnboxingBoxList($request);
    public function getBoxDimention($request);
}
