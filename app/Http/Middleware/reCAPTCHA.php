<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class reCAPTCHA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      if ($request->has('g-recaptcha-response')) {
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query([
                    //TODO: change this key
                    'secret' => config('captcha.secret'),
                    'response' => $request->input('g-recaptcha-response')
                ])
            ]
        ]);
        $result = json_decode(file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context),true);
        if ($result['success']) {
          return $next($request);
        }
      }
      abort(401);
    }
}
