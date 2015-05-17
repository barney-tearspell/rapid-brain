<?php namespace App\Providers;

use App\Services\RapidBrain;
use Illuminate\Support\ServiceProvider;

class RapidBrainProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Services\RapidBrain', function($app) {
			return new RapidBrain($app);
		});
    }
}
