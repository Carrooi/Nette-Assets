<?php

/**
 * Test: Carrooi\Assets\DI\IAssetsProvider
 *
 * @testCase CarrooiTests\Assets\DI\AssetsProviderTest
 * @author David Kudera
 */

namespace CarrooiTests\Assets\DI;

use Carrooi\Assets\DI\IAssetsProvider;
use Nette\Configurator;
use Nette\DI\CompilerExtension;
use Tester\Assert;
use Tester\FileMock;
use Tester\TestCase;

require_once __DIR__. '/../../bootstrap.php';

/**
 *
 * @author David Kudera
 */
class AssetsProviderTest extends TestCase
{


	/** @var \Carrooi\Assets\Assets */
	private $assets;


	/**
	 * @param string $configFile
	 */
	private function createContainer($configFile = null)
	{
		$config = new Configurator;
		$config->setTempDirectory(TEMP_DIR);
		$config->addParameters(['appDir' => __DIR__. '/../']);
		$config->addConfig(__DIR__. '/../config/config.neon');

		if ($configFile) {
			$config->addConfig($configFile);
		}

		$context = $config->createContainer();

		$this->assets = $context->getByType('Carrooi\Assets\Assets');
	}


	public function testFunctionality()
	{
		$this->createContainer(FileMock::create('{extensions: [CarrooiTests\Assets\DI\OtherAssetsProvider]}', 'neon'));

		$css = $this->assets->getResource('front', 'css');

		$files = [
			__DIR__. '/../files/css/style.css',
			__DIR__. '/../files/css/components/widgets/favorite.css',
			__DIR__. '/../files/css/components/footer.css',
			__DIR__. '/../files/css/components/menu.css',
			__DIR__. '/../files/css/core/mixins.css',
			__DIR__. '/../files/css/core/variables.css',
			__DIR__. '/../files/css/other.css',
		];
		$files = array_map(function($path) {
			return realpath($path);
		}, $files);
		sort($files);

		$actual = $css->getFiles();
		sort($actual);

		Assert::same($files, $actual);
	}

}


/**
 *
 * @author David Kudera
 */
class OtherAssetsProvider extends CompilerExtension implements IAssetsProvider
{


	/**
	 * @return array
	 */
	public function getAssetsFiles()
	{
		return [
			'front' => [
				'css' => [
					__DIR__. '/../files/css/other.css',
				]
			],
		];
	}

}


run(new AssetsProviderTest);
