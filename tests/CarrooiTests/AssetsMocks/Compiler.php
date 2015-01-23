<?php

namespace CarrooiTests\AssetsMocks;

use Carrooi\Assets\Compilers\BaseCompiler;

/**
 *
 * @author David Kudera
 */
class Compiler extends BaseCompiler
{


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

		return implode(',', $result);
	}


	/**
	 * @param string $publicPath
	 * @return string
	 */
	public function createHtml($publicPath)
	{
		return $publicPath;
	}

}
