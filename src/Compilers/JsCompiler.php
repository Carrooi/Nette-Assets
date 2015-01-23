<?php

namespace Carrooi\Assets\Compilers;

use Nette\Utils\Strings;

/**
 *
 * @author David Kudera
 */
class JsCompiler extends BaseCompiler
{


	public function __construct()
	{
		$this->addFileFilter(function($file) {
			return "(function() {\n". Strings::indent($file). "\n}).call();";
		});
	}


	/**
	 * @param string $publicPath
	 * @return string
	 */
	public function createHtml($publicPath)
	{
		return '<script type="text/javascript" src="'. $publicPath. '"></script>';
	}

}
