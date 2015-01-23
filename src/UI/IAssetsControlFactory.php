<?php

namespace Carrooi\Assets\UI;

/**
 *
 * @author David Kudera
 */
interface IAssetsControlFactory
{


	/**
	 * @return \Carrooi\Assets\UI\AssetsControl
	 */
	public function create();

}
