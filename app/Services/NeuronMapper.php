<?php namespace App\Services;

use Illuminate\Contracts\Filesystem\Factory as File;

class NeuronMapper {

	private $filesystem, $file, $data;

	public function __construct(File $filesystem, $file = 'neurons.json') {
		$this->filesystem = $filesystem;
		// preload and overwrite hardcoded neurons for fallbacks
		$this->loadNeurons('core.json');
		$this->loadNeurons($file);
	}

	private function loadNeurons( $file = 'neurons.json' ){
		$this->file = 'data' . DIRECTORY_SEPARATOR . $file;
		if ( ! $this->data) {
			$this->data = [];
		}
		if ( ! $this->filesystem->exists($this->file)) {
			$this->filesystem->put($this->file, json_encode([]));
		}
		$this->data = array_replace_recursive($this->data, json_decode($this->filesystem->get($this->file), true) );
	}

	public function find($synapse) {
		return array_get($this->getNeurons(), $synapse, '');
	}

	public function save(array $neurons) {
		// todo Validate neurons format here
		$neurons = array_merge($this->getNeurons(), $neurons);
		$this->data = $neurons;

		return $this->filesystem->put($this->file, json_encode($this->data));
	}

	protected function getNeurons() {
		if ($this->data) {
			return $this->data;
		}
/*
		if ( ! $this->filesystem->exists($this->file)) {
			$this->filesystem->put($this->file, json_encode([]));
		}
		return $this->data = json_decode($this->filesystem->get($this->file), true);
*/
		return [];
	}

}