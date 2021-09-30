<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $table = 'WEB_NEWSLETTER';
    protected $primaryKey = 'PK_NO';
    public $timestamps = false;

    public function getNewsletters($limit = 2000)
    {
        return Newsletter::paginate($limit);
    }
}
