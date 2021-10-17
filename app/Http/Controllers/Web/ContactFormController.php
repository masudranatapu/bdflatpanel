<?php

namespace App\Http\Controllers\Web;
use App\Models\Web\AboutUs;
use App\Models\Web\ContactForm;
use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;

class ContactFormController extends Controller
{

    protected $contact_message;

    public function __construct(ContactForm $contact_message)
    {
        $this->contact_message     = $contact_message;
    }

    public function getIndex(){
        $this->resp = $this->contact_message->getIndex();
        $data['contact_message'] = $this->resp->data;
        return view('admin.web.contact-message.index')->withData($data);
    }

}
