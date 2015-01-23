<?php

/**
 * Test: Carrooi\Assets\AssetsNamespace
 *
 * @testCase CarrooiTests\Assets\AssetsNamespaceTest
 * @author David Kudera
 */

namespace CarrooiTests\Assets;

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
class AssetsNamespaceTest extends TestCase
{


	public function testGetResource()
	{
		$namespace = new AssetsNamespace;
		$resource = new AssetsResource('test', new Compiler, new MemoryStorage);

		$namespace->addResource('test', $resource);

		Assert::same($resource, $namespace->getResource('test'));
	}


	public function testGetResource_notExists()
	{
		$namespace = new AssetsNamespace;

		Assert::exception(function() use ($namespace) {
			$namespace->getResource('test');
		}, 'Carrooi\Assets\AssetsResourceNotExists', 'Assets resource test does not exists.');
	}

}


run(new AssetsNamespaceTest);
