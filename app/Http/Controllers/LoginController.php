<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Utils\GISHttp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    public function index()
    {
        return view('pages.auth.login');
    }

    public function login(LoginRequest $request)
    {
        $http  = new GISHttp();
        $token = $http->login($request->validated());

        Session::put('token', $token['meta']['token']);

        return redirect(route('roads.index'));
    }
}
