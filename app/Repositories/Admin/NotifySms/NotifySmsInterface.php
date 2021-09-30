<?php

namespace App\Repositories\Admin\NotifySms;

interface NotifySmsInterface
{
    public function getPaginatedList($request, int $per_page = 5);
    public function getEmailIndex($request);
    public function getSendSms(int $id);
    public function getSendEmail(int $id);
    public function getEmailBody(int $id);
    public function getSendAllSms($request);
    public function getOrderDefault($request);

}
