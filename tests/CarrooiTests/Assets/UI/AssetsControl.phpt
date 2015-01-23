<?php

/**
 * Test: Carrooi\Assets\UI\AssetsControl
 *
 * @testCase CarrooiTests\Assets\UI\AssetsControlTest
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
class AssetsControlTest extends TestCase
{


	/** @var \Carrooi\Assets\Assets */
	private $assets;

	/** @var \Carrooi\Assets\UI\IAssetsControlFactory */
	private $controlFactory;


	public function setUp()
	{
		$config = new Configurator;
		$config->setTempDirectory(TEMP_DIR);
		$config->addParameters(['appDir' => __DIR__. '/../']);
		$config->addConfig(__DIR__. '/../config/config.neon');

		$context = $config->createContainer();

		$this->assets = $context->getByType('Carrooi\Assets\Assets');
		$this->controlFactory = $context->getByType('Carrooi\Assets\UI\IAssetsControlFactory');
	}


	public function tearDown()
	{
		@unlink(__DIR__. '/../files/_build.css');
	}


	public function testRender()
	{
		$control = $this->controlFactory->create();
		$resource = $this->assets->getResource('front', 'css');

		$resource->setDebugMode(true);

		ob_start();
		$control->render('front', 'css');
		$css = ob_get_clean();

		Assert::same('<link href="/files/_build.css?&v=1" rel="stylesheet" type="text/css">', $css);
		Assert::true(is_file($resource->getTarget()));
		Assert::same(1, $resource->getCurrentVersion());

		ob_start();
		$control->render('front', 'css');
		ob_clean();

		Assert::same(1, $resource->getCurrentVersion());

		unlink($resource->getTarget());

		ob_start();
		$control->render('front', 'css');
		ob_clean();

		Assert::same(2, $resource->getCurrentVersion());
	}

}


run(new AssetsControlTest);
