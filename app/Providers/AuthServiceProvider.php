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

        Gate::define('authorization', function (User $user, $controller_actions) {
            $authorizations=app('db')->table('authorizations')
                          ->where('auth', '=', $user->auth)
                          ->where('controller_actions', '=', $controller_actions)
                          ->count();
            if ($authorizations!=0) {
              return true;
            }
            return false;
        });
    }
}
