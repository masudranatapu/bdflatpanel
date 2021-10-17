<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Web\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    protected $newsletter;

    public function __construct(Newsletter $newsletter)
    {
        $this->newsletter = $newsletter;
    }

    public function getIndex()
    {
        $newsletters = $this->newsletter->getNewsletters();
        return view('admin.web.newsletter.index')->withRows($newsletters);
    }
}
