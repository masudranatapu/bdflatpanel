<?php


namespace App\Repositories\Admin\PropertyCondition;


interface PropertyConditionInterface
{
    public function getPropertyConditions($limit = 2000);

    public function getPropertyCondition(int $id);

    public function postStore($request);

    public function postUpdate($request, int $id);

    public function getDelete(int $id);
}
