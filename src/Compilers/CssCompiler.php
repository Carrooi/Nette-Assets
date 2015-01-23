<?php

namespace Carrooi\Assets\Compilers;

/**
 *
 * @author David Kudera
 */
class CssCompiler extends BaseCompiler
{


	/**
	 * @param string $publicPath
	 * @return string
	 */
	public function createHtml($publicPath)
	{
		return '<link href="'. $publicPath. '" rel="stylesheet" type="text/css">';
	}

}
