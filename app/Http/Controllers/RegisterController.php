<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Utils\GISHttp;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function index()
    {
        return view('pages.auth.register');
    }

    public function store(RegisterRequest $request)
    {
        $http = new GISHttp();
        $http->register($request->validated());

        return redirect(route('login.index'));
    }
}
