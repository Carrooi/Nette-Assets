<?php

/**
 * Test: Carrooi\Assets\Assets
 *
 * @testCase CarrooiTests\Assets\AssetsTest
 * @author David Kudera
 */

namespace CarrooiTests\Assets;

use Carrooi\Assets\Assets;
use Carrooi\Assets\AssetsNamespace;
use Carrooi\Assets\AssetsResource;
use CarrooiTests\AssetsMocks\Compiler;
use Nette\Caching\Storages\MemoryStorage;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__. '/../bootstrap.php';

/**
 *
 * @author David Kudera
 */
class AssetsTest extends TestCase
{


	public function testGetNamespace()
	{
		$assets = new Assets;
		$namespace = new AssetsNamespace;

		$assets->addNamespace('test', $namespace);

		Assert::same($namespace, $assets->getNamespace('test'));
	}


	public function testGetNamespace_notExists()
	{
		$assets = new Assets;
		$assets->addNamespace('test', new AssetsNamespace);

		Assert::exception(function() use ($assets) {
			$assets->getNamespace('test2');
		}, 'Carrooi\Assets\AssetsNamespaceNotExists', 'Assets namespace test2 does not exists.');
	}


	public function testGetResource()
	{
		$assets = new Assets;
		$namespace = new AssetsNamespace;
		$resource = new AssetsResource('test', new Compiler, new MemoryStorage);

		$assets->addNamespace('test', $namespace);
		$namespace->addResource('css', $resource);

		Assert::same($resource, $assets->getResource('test', 'css'));
	}

}


run(new AssetsTest);
