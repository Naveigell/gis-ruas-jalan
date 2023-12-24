<?php

namespace App\Http\Middleware;

use App\Utils\GISHttp;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Session;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login.index');
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string[] ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $http = new GISHttp();
        $http->setToken(Session::get('token'));

        $response = $http->checkUser();

        // if 'code' is exists in response, it's mean http_code is 404,
        // if not, it's mean http_code is 200
        //
        // i don't know why the response is like this
        if (array_key_exists('code', $response)) {
            return redirect()->route('login.index');
        }

        return $next($request);
    }
}
