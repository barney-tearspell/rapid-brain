<?php namespace App\Services;

use Laravel\Lumen\Application;

class RapidBrain {

	protected $app;

	public function __construct(Application $app)
	{
		$this->app = $app;
	}


	public function lucidity()
	{
		$this->setupSynapseRoute();
	}


	protected function setupSynapseRoute()
	{
		$this->app->get('{synapse?}', function($synapse) { return $this->getSynapse($synapse); });
	}


	protected function getSynapse($synapse)
	{
		dd($synapse);
	}

}