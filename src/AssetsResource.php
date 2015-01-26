<?php

namespace Carrooi\Assets;

use Carrooi\Assets\Compilers\BaseCompiler;
use Carrooi\Helpers\FileSystemHelpers;
use Nette\Caching\Cache;
use Nette\Caching\IStorage;
use Nette\Object;

/**
 *
 * @author David Kudera
 */
class AssetsResource extends Object
{


	const CACHE_NAMESPACE = 'carrooi.assets';


	/** @var string */
	private $namespace;

	/** @var \Carrooi\Assets\Compilers\BaseCompiler */
	private $compiler;

	/** @var \Nette\Caching\Cache */
	private $cache;

	/** @var array */
	private $paths = [];

	/** @var string */
	private $target;

	/** @var string */
	private $publicPath;

	/** @var bool */
	private $debugMode = false;

	/** @var array */
	private $files;

	/** @var array */
	private $times = [];

	/** @var callable[] */
	private $filters = [];

	/** @var string */
	private $output;

	/** @var int */
	private $rebuildReason;


	/**
	 * @param string $namespace
	 * @param \Carrooi\Assets\Compilers\BaseCompiler $compiler
	 * @param \Nette\Caching\IStorage $storage
	 */
	public function __construct($namespace, BaseCompiler $compiler, IStorage $storage)
	{
		$this->namespace = $namespace;
		$this->compiler = $compiler;
		$this->cache = new Cache($storage, self::CACHE_NAMESPACE);
	}


	/**
	 * @return \Carrooi\Assets\Compilers\BaseCompiler
	 */
	public function getCompiler()
	{
		return $this->compiler;
	}


	/**
	 * @param string $path
	 * @return $this
	 */
	public function addPath($path)
	{
		$this->paths[] = $path;
		return $this;
	}


	/**
	 * @return array
	 */
	public function getPaths()
	{
		return $this->paths;
	}


	/**
	 * @return array
	 */
	public function getFiles()
	{
		if ($this->files === null) {
			$this->files = FileSystemHelpers::expandFiles($this->getPaths());
		}

		return $this->files;
	}


	/**
	 * @return string
	 */
	public function getTarget()
	{
		return $this->target;
	}


	/**
	 * @param string $target
	 * @return $this
	 */
	public function setTarget($target)
	{
		$this->target = $target;
		return $this;
	}


	/**
	 * @param bool $versioned
	 * @return string
	 */
	public function getPublicPath($versioned = false)
	{
		$path = $this->publicPath;
		if ($versioned) {
			$path .= '?&v='. $this->getCurrentVersion();
		}

		return $path;
	}


	/**
	 * @param string $path
	 * @return $this
	 */
	public function setPublicPath($path)
	{
		$this->publicPath = $path;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function isDebugMode()
	{
		return $this->debugMode === true;
	}


	/**
	 * @param bool $debugMode
	 * @return $this
	 */
	public function setDebugMode($debugMode = true)
	{
		$this->debugMode = $debugMode;
		return $this;
	}


	/**
	 * @param string $data
	 * @param callable $fallback
	 * @return mixed
	 */
	private function loadCacheData($data, callable $fallback = null)
	{
		return $this->cache->load($this->namespace. '.'. $data, $fallback);
	}


	/**
	 * @param string $key
	 * @param mixed $data
	 */
	private function saveCacheData($key, $data)
	{
		$this->cache->save($this->namespace. '.'. $key, $data);
	}


	/**
	 * @return int
	 */
	public function getCurrentVersion()
	{
		return (int) $this->loadCacheData('version');
	}


	/**
	 * @return int
	 */
	private function increaseVersion()
	{
		$version = $this->getCurrentVersion();

		if ($version === 0) {
			$version = 1;
		} else {
			$version++;
		}

		$this->saveCacheData('version', $version);

		return $version;
	}


	/**
	 * @return array
	 */
	private function getOldFiles()
	{
		return $this->loadCacheData('files', function() {
			return [];
		});
	}


	/**
	 * @param string $path
	 * @return int
	 */
	private function getFileModified($path)
	{
		if (!isset($this->times[$path])) {
			$this->times[$path] = filemtime($path);
		}

		return $this->times[$path];
	}


	/**
	 * @param callable $filter
	 * @return $this
	 */
	public function addFilter(callable $filter)
	{
		$this->filters[] = $filter;
		return $this;
	}


	/**
	 * @return string
	 */
	public function getOutput()
	{
		if (!$this->output) {
			if ($this->needsRebuild()) {
				$this->build();
			} else {
				$this->output = file_get_contents($this->getTarget());
			}
		}

		return $this->output;
	}


	/**
	 * @return int
	 */
	public function getRebuildReason()
	{
		return $this->rebuildReason;
	}


	/**
	 * @return string
	 */
	public function createHtml()
	{
		return $this->compiler->createHtml($this->getPublicPath(true));
	}


	public function build()
	{
		$files = $this->getFiles();
		if (count($files) === 0) {
			throw new InvalidStateException('Missing files to build in '. $this->namespace. ' assets resource.');
		}

		$timedFiles = [];
		foreach ($files as $file) {
			$timedFiles[$file] = $this->getFileModified($file);
		}

		$this->increaseVersion();

		$this->saveCacheData('files', $timedFiles);

		$output = $this->compiler->compile($files);

		foreach ($this->filters as $filter) {
			$output = $filter($output);
		}

		$this->output = $output;

		file_put_contents($this->getTarget(), $output);
	}


	/**
	 * @return bool
	 */
	public function needsRebuild()
	{
		$target = $this->getTarget();
		if (!$target) {
			throw new InvalidStateException('You have to set target for '. $this->namespace. ' assets resource.');
		}

		if (is_file($target)) {
			if ($this->isDebugMode()) {
				$files = $sortedFiles = $this->getFiles();
				if (count($files) === 0) {
					throw new InvalidStateException('Missing files to build in '. $this->namespace. ' assets resource.');
				}

				$oldFiles = $this->getOldFiles();
				$oldFilesPaths = array_keys($oldFiles);

				sort($sortedFiles);
				sort($oldFilesPaths);

				if ($sortedFiles === $oldFilesPaths) {
					foreach ($files as $file) {
						if ($this->getFileModified($file) !== $oldFiles[$file]) {
							$this->rebuildReason = Assets::REBUILD_REASON_FILES_CHANGES;
							return true;
						}
					}

					$this->rebuildReason = null;
					return false;
				} else {
					$this->rebuildReason = Assets::REBUILD_REASON_DIFFERENT_FILES;
					return true;
				}
			} else {
				$this->rebuildReason = null;
				return false;
			}
		} else {
			$this->rebuildReason = Assets::REBUILD_REASON_MISSING_TARGET;
			return true;
		}
	}

}
