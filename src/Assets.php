<?php

namespace Carrooi\Assets;

use Nette\Object;

/**
 *
 * @author David Kudera
 */
class Assets extends Object
{


	const REBUILD_REASON_MISSING_TARGET = 1;

	const REBUILD_REASON_DIFFERENT_FILES = 2;

	const REBUILD_REASON_FILES_CHANGES = 3;


	/** @var \Carrooi\Assets\AssetsNamespace[] */
	private $namespaces = [];


	/**
	 * @param string $name
	 * @param \Carrooi\Assets\AssetsNamespace $namespace
	 * @return $this
	 */
	public function addNamespace($name, AssetsNamespace $namespace)
	{
		$this->namespaces[$name] = $namespace;
		return $this;
	}


	/**
	 * @param string $name
	 * @return \Carrooi\Assets\AssetsNamespace
	 */
	public function getNamespace($name)
	{
		if (!isset($this->namespaces[$name])) {
			throw new AssetsNamespaceNotExists('Assets namespace '. $name. ' does not exists.');
		}

		return $this->namespaces[$name];
	}


	/**
	 * @param string $namespace
	 * @param string $resource
	 * @return \Carrooi\Assets\AssetsResource
	 */
	public function getResource($namespace, $resource)
	{
		return $this->getNamespace($namespace)->getResource($resource);
	}

}
