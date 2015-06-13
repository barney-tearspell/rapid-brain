<?php namespace App\Providers;

use App\Services\RapidBrain;
use Illuminate\Support\ServiceProvider;
use App\Services\NeuronMapper;
use Illuminate\Http\Request;

class RapidBrainProvider extends ServiceProvider {

    public function boot()
    {
        $brain = $this->app->make('App\Services\RapidBrain');

        $this->app->get('{synapse:.*}', function(NeuronMapper $mapper, $synapse) use ($brain) {
            return $brain->activateSynapse($mapper, $synapse); 
        });
        $this->app->post('{synapse:.*}', function(Request $request, NeuronMapper $mapper, $synapse) use ($brain) { 
            return $brain->saveNeurons($mapper, $synapse, $request->all()); 
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Services\NeuronMapper', function($app) {
            return new NeuronMapper(storage_path('data' . DIRECTORY_SEPARATOR . 'neurons.json'));
        });
        $this->app->singleton('App\Services\RapidBrain', function($app) {
			return new RapidBrain($app->make('App\Services\NeuronMapper'));
		});
    }
}
