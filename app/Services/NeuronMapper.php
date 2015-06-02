<?php namespace App\Services;

class NeuronMapper {

	private $filename;

	private static $data;

	public function __construct($filename) {
		$this->filename = $filename;
		if ( ! $this->getData()) {
			$this->setData(array());
		}
	}

	public function find($synapse) {
		$neurons = $this->getNeurons();
		return isset($neurons[$synapse]) ? $neurons[$synapse] : '';
	}

	public function save(array $neurons) {
		// todo Validate neurons format here
		$neurons = array_merge($this->getNeurons(), $neurons);
		$this->setData($neurons);

		return $this->put($this->filename, json_encode($this->getData()));
	}

	protected function getNeurons() {
		if ($data = $this->getData()) {
			return $data;
		}

		if ( ! file_exists($this->filename)) {
			$this->put($this->filename, json_encode(array()));
		}

		return $this->setData(json_decode($this->get($this->filename), true))->getData();
	}

	protected function setData(array $data) {
		static::$data[$this->filename] = $data;

		return $this;
	}

	protected function getData() {
		return isset(static::$data[$this->filename]) ? static::$data[$this->filename] : null;
	}

	protected function put($filename, $data) {
		if( ! is_dir($dir = dirname($filename))) {
			mkdir($dir);
		}
		return file_put_contents($filename, $data, LOCK_EX);
	}

	protected function get($filename) {
		return file_get_contents($filename);
	}

}