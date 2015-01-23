<?php

namespace Carrooi\Assets\Compilers;

use Nette\Object;

/**
 *
 * @author David Kudera
 */
abstract class BaseCompiler extends Object
{


	/** @var callable[] */
	public $fileFilters = [];


	/**
	 * @param string $publicPath
	 * @return string
	 */
	abstract public function createHtml($publicPath);


	/**
	 * @param callable $filter
	 * @return $this
	 */
	public function addFileFilter(callable $filter)
	{
		$this->fileFilters[] = $filter;
		return $this;
	}


	/**
	 * @param array $files
	 * @return string
	 */
	public function compile(array $files)
	{
		$result = [];
		foreach ($files as $file) {
			$result[] = $this->loadFile($file);
		}

		return implode("\n", $result). "\n";
	}


	/**
	 * @param string $path
	 * @return string
	 */
	protected function loadFile($path)
	{
		$file = file_get_contents($path);

		foreach ($this->fileFilters as $filter) {
			$file = $filter($file);
		}

		return $file;
	}

}
