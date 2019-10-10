<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Facebook\Facebook;

class FacebookServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(Facebook::class, function ($app) {
            $config = config('services.facebook');
            $fb = new Facebook([
                'app_id' => $config['client_id'],
                'app_secret' => $config['client_secret'],
                'default_graph_version' => $config['default_graph_version'],
            ]);
            $fb->setDefaultAccessToken($config['access_token']);
            return $fb;
        });
    }
}
