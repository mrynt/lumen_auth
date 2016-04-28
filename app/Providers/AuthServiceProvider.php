<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request) {
            if ($request->header('Authorization')) {
                $datetime=date('Y-m-d H:i:s', time());
                return User:: where('api_token', '=', $request->header('Authorization'))
                              ->where('confirmed', '=', true)
                              ->where('expires_at', '>', $datetime)
                              ->first();
            }
        });

        //Authorizations

        Gate::define('authorization', function (User $user, $controller, $action) {
            $authorizations=app('db')->table('authorizations')
                          ->select('controller', 'action')
                          ->where('auth', '=', $user->auth)
                          ->get();
            foreach ($authorizations as $authorization) {
              if ($authorization->controller==$controller && $authorization->action==$action) {
                return true;
              }
            }
            return false;
        });
    }
}
