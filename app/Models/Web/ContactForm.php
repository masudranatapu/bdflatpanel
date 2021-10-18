<?php
namespace App\Models\Web;
use App\Traits\RepoResponse;
use Illuminate\Database\Eloquent\Model;

class ContactForm extends Model
{
    use RepoResponse;
    protected $table        = 'WEB_CONTACT_MESSAGE';
    protected $primaryKey   = 'PK_NO';
    public $timestamps      = false;

    public function getIndex()
    {
        $data =  ContactForm::get();
        if (!empty($data)) {
            return $this->formatResponse(true, 'Data found', 'admin.web.contact_message', $data);
        }
        return $this->formatResponse(false, 'Did not found data !', 'admin.web.contact_message', null);
    }
}
