<?php

namespace Carrooi\Assets\UI;

use Carrooi\Assets\Assets;
use Nette\Application\UI\Control;

/**
 *
 * @author David Kudera
 */
class AssetsControl extends Control
{


	/** @var \Carrooi\Assets\Assets */
	private $assets;


	/**
	 * @param \Carrooi\Assets\Assets $assets
	 */
	public function __construct(Assets $assets)
	{
		parent::__construct();

		$this->assets = $assets;
	}


	/**
	 * @param string $namespace
	 * @param string $resource
	 */
	public function render($namespace, $resource)
	{
		$resource = $this->assets->getResource($namespace, $resource);

		if ($resource->needsRebuild()) {
			$resource->build();
		}

		echo $resource->createHtml();
	}

}
