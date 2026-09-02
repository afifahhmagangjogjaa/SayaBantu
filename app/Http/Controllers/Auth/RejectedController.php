<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use Illuminate\Http\Request;

class RejectedController extends Controller
{
    public function show(Registration $registration)
    {
        return view('auth.rejected', ['registration' => $registration]);
    }
}
