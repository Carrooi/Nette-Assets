<?php

/**
 * Test: Carrooi\Assets\DI\AssetsExtension
 *
 * @testCase CarrooiTests\Assets\DI\AssetsExtensionTest
 * @author David Kudera
 */

namespace CarrooiTests\Assets\DI;

use Nette\Configurator;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__. '/../../bootstrap.php';

/**
 *
 * @author David Kudera
 */
class AssetsExtensionTest extends TestCase
{


	/** @var \Carrooi\Assets\Assets */
	private $assets;


	/**
	 * @return \Nette\DI\Container
	 */
	private function createContainer()
	{
		$config = new Configurator;
		$config->setTempDirectory(TEMP_DIR);
		$config->addParameters(['appDir' => __DIR__. '/../']);
		$config->addConfig(__DIR__. '/../config/config.neon');

		$context = $config->createContainer();

		$this->assets = $context->getByType('Carrooi\Assets\Assets');
	}


	public function testFunctionality()
	{
		$this->createContainer();

		$css = $this->assets->getResource('front', 'css');

		Assert::true($css->isDebugMode());
		Assert::same('/files/_build.css', $css->getPublicPath());
		Assert::notSame(null, $css->getTarget());
	}

}


run(new AssetsExtensionTest);
