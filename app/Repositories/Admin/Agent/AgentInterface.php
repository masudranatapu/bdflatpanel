<?php

namespace App\Repositories\Admin\Agent;

interface AgentInterface
{
    public function getPaginatedList($request, int $per_page = 5);

    public function getAgent(int $id);

    public function postStore($request);

    public function postUpdate($request, int $id);

    public function delete($id);
}
