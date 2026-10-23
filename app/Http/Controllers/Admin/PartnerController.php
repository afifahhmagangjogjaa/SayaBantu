<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class PartnerController extends Controller
{
    public function blocked()
    {
        return view('admin.partners.blocked');
    }
}
