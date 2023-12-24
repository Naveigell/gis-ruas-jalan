<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LogoutController extends Controller
{
    public function store()
    {
        Session::forget('token');

        return redirect(route('login.index'));
    }
}
