<?php namespace App\Services;

use Laravel\Lumen\Application;
use App\Services\NeuronMapper;

class RapidBrain {

	const SCOPE_SEPARATOR = '.';

	protected $app, $mapper;

	public function __construct(Application $app, NeuronMapper $mapper)
	{
		$this->app = $app;
		$this->mapper = $mapper;
	}


	public function lucidity()
	{
		$this->setupSynapseRoute();
	}


	protected function setupSynapseRoute()
	{
		$brain = $this;
		$this->app->get('{synapse:.*}', function(NeuronMapper $mapper, $synapse) use ($brain) { 
			if (FALSE) {
				$mapper->save([
					'global-header' => ['value' => '<h1>Site</h1><nav><ul><li><a href="/">Home</a></li><li><a href="/about">About</a></li><li><a href="/contact">Contact</a></li></ul></nav>'],
					'/' => ['value' => '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Home</title></head><body><header data-src="global-header">{{global-header}}</header><h1>Home</h1><footer data-src="global-footer">{{global-footer}}</footer></body></html>'],
					'about' => ['value' => '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Home</title></head><body><header data-src="global-header">{{global-header}}</header><h1>About</h1><footer data-src="global-footer">{{global-footer}}</footer></body></html>']
				]);
			}
			return $brain->handleUrl($mapper, $synapse); 
		});
	}


	protected function getSynapse($synapse)
	{
		dd($synapse);
	}


	protected function handleUrl(NeuronMapper $mapper, $synapse)
	{
		$synapse =  $synapse ?: '/';
		
		$neuron = $mapper->find($synapse);
		if ($neuron)
		{
			$render = $this->renderNeuron($neuron);
			if (env('APP_DEBUG'))
			{
				$style = '<style>span.synapse{background:red;color:white;}</style>';
				if (strpos($render, '</head>') !== FALSE)
				{
					$render = str_replace('</head>', $style . '</head>', $render);
				}
				else
				{
					$render = $style . $render;
				}
			}
			echo $render;
		}
		else
		{
			return '404';
		}
	}

	protected function renderNeuron($neuron, $root = NULL)
	{
		$root = $root ?: $neuron;
		$synapses = array();
		$value = array_get($neuron, 'value');
		preg_match_all('/\{\{(.+?)\}\}/i', $value, $synapses);
		//dd($neuron, $synapses);
		if ( ! count($synapses[1]))
		{
			return $value;
		}
		foreach($synapses[1] as $synapse)
		{
			$render = env('APP_DEBUG') ? '<span class="synapse">' . $synapse . '</span>' : '';
			if (strpos($synapse, '$this' . self::SCOPE_SEPARATOR) === 0)
			{
				$render = $this->renderNeuronProperty($neuron, $this->getScopedSynapse($synapse)[1]) ?: $render;
			}
			elseif (strpos($synapse, '$page' . self::SCOPE_SEPARATOR) === 0)
			{
				$render = $this->renderNeuronProperty($root, $this->getScopedSynapse($synapse)[1]) ?: $render;
			}
			elseif (strpos($synapse, '$site' . self::SCOPE_SEPARATOR) === 0)
			{
				$render = $this->app->get(strtoupper($this->getScopedSynapse($synapse[1]))) ?: $render;
			}
			else
			{
				$neuron = $this->mapper->find($synapse);
				if ($neuron)
				{
					$render = $this->renderNeuron($neuron, $root);
				}
			}
			$value = str_replace('{{' . $synapse . '}}', $render, $value);
			$value = str_replace('data-source="' . $synapse . '"', '', $value);
		}
		return $value;
	}

	protected function getScopedSynapse($synapse)
	{
		return explode(self::SCOPE_SEPARATOR, $synapse);
	}

	public function renderNeuronProperty($neuron, $property)
	{
		return array_get(array_get($neuron, 'data', []), $property, '');
	}

}