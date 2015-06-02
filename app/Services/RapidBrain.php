<?php namespace App\Services;

use Laravel\Lumen\Application;
use App\Services\NeuronMapper;

class RapidBrain {

	const SCOPE_SEPARATOR = '.';
	const RENDER_HTML = 'html';
	const RENDER_JSON = 'json';

	protected $mapper;

	public function __construct(NeuronMapper $mapper)
	{
		$this->mapper = $mapper;
	}


	protected function getSynapse($synapse)
	{
		dd($synapse);
	}


	public function activateSynapse(NeuronMapper $mapper, $synapse)
	{
		$renderType = self::RENDER_HTML;

		if (preg_match('/\.html$/', $synapse))
		{
			$synapse = str_replace('.html', '', $synapse);
		}
		if (preg_match('/\.json$/', $synapse))
		{
			$synapse = str_replace('.json', '', $synapse);
			$renderType = self::RENDER_JSON;
		}

		$synapse =  $synapse ?: '/';
		
		$neuron = $mapper->find($synapse);
		if ($neuron)
		{
			$render = $this->renderNeuron($synapse, $neuron, $renderType);
			if (env('APP_DEBUG') && $renderType == self::RENDER_HTML)
			{
				$append = '<style>span.synapse{background:red;color:white;}</style>';
				$append .= '<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>';
				$append .= '<script src="/app.js"></script>';
				if (strpos($render, '</head>') !== FALSE)
				{
					$render = str_replace('</head>', $append . '</head>', $render);
				}
				else
				{
					$render = $append . $render;
				}
			}

			if ($renderType == self::RENDER_HTML)
			{
				return $render;
			}
			elseif ($renderType == self::RENDER_JSON)
			{
				return response()->json($render);
			}
		}
		else
		{
			return response(view('404'), 404);
		}
	}

	protected function renderNeuron($synapse, $neuron, $renderType, $root = NULL)
	{
		$root = $root ?: $neuron;
		$synapses = array();
		$value = array_get($neuron, 'value');
		preg_match_all('/\{\{(.+?)\}\}/i', $value, $synapses);
		
		switch ($renderType)
		{
			case self::RENDER_HTML:
				return $this->renderNeuronHtml($synapse, $neuron, $synapses[1], $root);
			case self::RENDER_JSON:
				return $this->renderNeuronArray($synapse, $neuron, $synapses[1], $root);
		}

		return null;
	}

	protected function renderNeuronHtml($synapse, $neuron, $synapses, $root)
	{
		$value = array_get($neuron, 'value');
		if ( ! count($synapses))
		{
			return $value;
		}
		foreach($synapses as $_synapse)
		{
			$render = env('APP_DEBUG') ? '<span class="synapse">' . $_synapse . '</span>' : '';
			if (strpos($_synapse, '$this' . self::SCOPE_SEPARATOR) === 0)
			{
				$render = $this->renderNeuronProperty($neuron, $this->getScopedSynapse($_synapse)[1]) ?: $render;
			}
			elseif (strpos($_synapse, '$page' . self::SCOPE_SEPARATOR) === 0)
			{
				$render = $this->renderNeuronProperty($root, $this->getScopedSynapse($_synapse)[1]) ?: $render;
			}
			elseif (strpos($_synapse, '$site' . self::SCOPE_SEPARATOR) === 0)
			{
				$render = env(strtoupper($this->getScopedSynapse($_synapse[1]))) ?: $render;
			}
			else
			{
				$neuron = $this->mapper->find($_synapse);
				if ($neuron)
				{
					$render = $this->renderNeuron($_synapse, $neuron, self::RENDER_HTML, $root);
				}
			}
			$value = str_replace('{{' . $_synapse . '}}', $render, $value);
			$value = str_replace('data-source="' . $_synapse . '"', '', $value);
		}

		return $value;
	}

	protected function renderNeuronArray($synapse, $neuron, $synapses, $root)
	{
		$array = [$synapse => $neuron];
		if ( ! count($synapses))
		{
			return $array;
		}

		foreach($synapses as $_synapse)
		{
			$neuron = $this->mapper->find($_synapse);
			if ($neuron)
			{
				$array = array_merge($array, (array) $this->renderNeuron($_synapse, $neuron, self::RENDER_JSON, $root));
			}
		}

		return $array;
	}

	protected function getScopedSynapse($synapse)
	{
		return explode(self::SCOPE_SEPARATOR, $synapse);
	}

	public function renderNeuronProperty($neuron, $property)
	{
		return array_get(array_get($neuron, 'data', []), $property, '');
	}

	public function saveNeurons(NeuronMapper $mapper, $synapse, $neurons)
	{
		$redirect = false;
		if (isset($neurons['neuron']) && ! is_array($neurons['neuron']))
		{
			$synapse = $synapse ?: '/';
			$neurons = [
				$synapse => [
					'value' => $neurons['neuron']
				]
			];
			$redirect = true;
		}

		$this->validateNeurons($neurons);
		$mapper->save($neurons);

		return $redirect ? redirect($synapse) : response()->json($neurons);
	}


	protected function validateNeurons(array $neurons)
	{
		foreach($neurons as $neuron)
		{
			if ( ! isset ($neuron['value']))
			{
				throw new \InvalidArgumentException('Neuron must contain "value" property!');
			}
		}
	}

}