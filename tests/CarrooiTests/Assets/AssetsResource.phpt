<?php

/**
 * Test: Carrooi\Assets\AssetsResource
 *
 * @testCase CarrooiTests\Assets\AssetsResourceTest
 * @author David Kudera
 */

namespace CarrooiTests\Assets;

use Carrooi\Assets\Assets;
use Carrooi\Assets\AssetsResource;
use CarrooiTests\AssetsMocks\Compiler;
use Nette\Caching\Cache;
use Nette\Caching\Storages\MemoryStorage;
use Tester\Assert;
use Tester\FileMock;
use Tester\TestCase;

require_once __DIR__. '/../bootstrap.php';

/**
 *
 * @author David Kudera
 */
class AssetsResourceTest extends TestCase
{


	/** @var \Nette\Caching\Storages\MemoryStorage */
	private $storage;

	/** @var \Carrooi\Assets\AssetsResource */
	private $resource;


	public function setUp()
	{
		$this->storage = new MemoryStorage;
		$compiler = new Compiler;

		$this->resource = new AssetsResource('test', $compiler, $this->storage);
		$this->resource->setTarget(FileMock::create(''));
		$this->resource->setPublicPath('style.css');
	}


	public function testGetCurrentVersion()
	{
		Assert::same(0, $this->resource->getCurrentVersion());
	}


	public function testNeedsRebuild_throwNoTarget()
	{
		$this->resource->setTarget(null);
		Assert::exception(function() {
			$this->resource->needsRebuild();
		}, 'Carrooi\Assets\InvalidStateException', 'You have to set target for test assets resource.');
	}


	public function testNeedsRebuild_targetNotExists()
	{
		$this->resource->setTarget(__DIR__. '/style.css');
		Assert::true($this->resource->needsRebuild());
		Assert::same(Assets::REBUILD_REASON_MISSING_TARGET, $this->resource->getRebuildReason());
	}


	public function testNeedsRebuild_production()
	{
		Assert::false($this->resource->needsRebuild());
	}


	public function testNeedsRebuild_noFiles()
	{
		$this->resource->setDebugMode(true);
		Assert::exception(function() {
			$this->resource->needsRebuild();
		}, 'Carrooi\Assets\InvalidStateException', 'Missing files to build in test assets resource.');
	}


	public function testNeedsRebuild_filesNotMatch()
	{
		$cache = new Cache($this->storage, AssetsResource::CACHE_NAMESPACE);
		$cache->save('test.files', [
			'/path/to/file' => 555,
		]);

		$this->resource->setDebugMode(true);
		$this->resource->addPath(FileMock::create(''));

		Assert::true($this->resource->needsRebuild());
		Assert::same(Assets::REBUILD_REASON_DIFFERENT_FILES, $this->resource->getRebuildReason());
	}


	public function testNeedsRebuild_fileModified()
	{
		$file = FileMock::create('body {}');
		$cache = new Cache($this->storage, AssetsResource::CACHE_NAMESPACE);
		$cache->save('test.files', [
			$file => 555,
		]);

		$this->resource->setDebugMode(true);
		$this->resource->addPath($file);

		Assert::true($this->resource->needsRebuild());
		Assert::same(Assets::REBUILD_REASON_FILES_CHANGES, $this->resource->getRebuildReason());
	}


	public function testNeedsRebuild()
	{
		$file = FileMock::create('body {}');
		$cache = new Cache($this->storage, AssetsResource::CACHE_NAMESPACE);
		$cache->save('test.files', [
			$file => filemtime($file),
		]);

		$this->resource->setDebugMode(true);
		$this->resource->addPath($file);

		Assert::false($this->resource->needsRebuild());
	}


	public function testBuild_noFiles()
	{
		Assert::exception(function() {
			$this->resource->build();
		}, 'Carrooi\Assets\InvalidStateException', 'Missing files to build in test assets resource.');
	}


	public function testBuild()
	{
		$this->resource->addPath(FileMock::create(''));
		$this->resource->addPath(FileMock::create(''));
		$this->resource->addPath(FileMock::create(''));

		Assert::same(0, $this->resource->getCurrentVersion());

		$this->resource->build();

		Assert::same(1, $this->resource->getCurrentVersion());

		$this->resource->build();

		Assert::same(2, $this->resource->getCurrentVersion());
	}


	public function testGetOutput()
	{
		$this->resource->addPath(FileMock::create('a'));
		$this->resource->addPath(FileMock::create('b'));
		$this->resource->addPath(FileMock::create('c'));

		$this->resource->setDebugMode(true);

		Assert::same('a,b,c', $this->resource->getOutput());
	}


	public function testGetOutput_productionExists()
	{
		$this->resource->setTarget(FileMock::create('a,b,c'));

		Assert::same('a,b,c', $this->resource->getOutput());
	}


	public function testGetPublicPath()
	{
		Assert::same('style.css', $this->resource->getPublicPath());
	}


	public function testGetPublicPath_versioned()
	{
		Assert::same('style.css?&v=0', $this->resource->getPublicPath(true));
	}


	public function testCreateHtml()
	{
		Assert::same('style.css?&v=0', $this->resource->createHtml());
	}

}


run(new AssetsResourceTest);
