<?php

namespace Carrooi\Assets\DI;

use Carrooi\Assets\InvalidArgumentException;
use Nette\DI\CompilerExtension;
use Nette\DI\Config\Helpers;
use Nette\PhpGenerator\PhpLiteral;

/**
 *
 * @author David Kudera
 */
class AssetsExtension extends CompilerExtension
{


	/** @var array */
	private $defaults = [
		'debug' => '%debugMode%',
	];

	/** @var array */
	private $namespaceDefaults = [];

	/** @var array */
	private $resourceDefaults = [
		'compiler' => null,
		'paths' => [],
		'target' => null,
		'publicPath' => null,
	];

	/** @var array */
	private $compilersAliases = [
		'css' => 'Carrooi\Assets\Compilers\CssCompiler',
		'js' => 'Carrooi\Assets\Compilers\JsCompiler',
	];


	public function loadConfiguration()
	{
		$builder = $this->getContainerBuilder();
		$config = $this->getConfig($this->defaults);

		$debugMode = $config['debug'];
		unset($config['debug']);

		$assets = $builder->addDefinition($this->prefix('assets'))
			->setClass('Carrooi\Assets\Assets');

		$builder->addDefinition($this->prefix('control'))
			->setClass('Carrooi\Assets\UI\AssetsControl')
			->setImplement('Carrooi\Assets\UI\IAssetsControlFactory');

		foreach ($this->compiler->getExtensions('Carrooi\Assets\DI\IAssetsProvider') as $extension) {
			/** @var \Carrooi\Assets\DI\IAssetsProvider $extension */

			$config = Helpers::merge($config, $extension->getAssetsConfiguration());
		}

		foreach ($config as $name => $namespace) {
			$namespace = Helpers::merge($namespace, $this->namespaceDefaults);

			$namespaceName = $this->prefix('namespace.'. $name);
			$namespaceDef = $builder->addDefinition($namespaceName)
				->setClass('Carrooi\Assets\AssetsNamespace')
				->setAutowired(false);

			foreach ($namespace as $rName => $resource) {
				$resource = Helpers::merge($resource, $this->resourceDefaults);

				$resourceName = $this->prefix('resource.'. $name. '.'. $rName);
				$resourceDef = $builder->addDefinition($resourceName)
					->setClass('Carrooi\Assets\AssetsResource')
					->setArguments([$name. '.'. $rName, $this->parseCompiler($resource['compiler'])])
					->addSetup('setTarget', [$resource['target']])
					->addSetup('setPublicPath', [$resource['publicPath']])
					->addSetup('setDebugMode', [$debugMode])
					->setAutowired(false);

				foreach ($resource['paths'] as $path) {
					$resourceDef->addSetup('addPath', [$path]);
				}

				$namespaceDef->addSetup('$service->addResource(?, $this->getService(?))', [$rName, $resourceName]);
			}

			$assets->addSetup('$service->addNamespace(?, $this->getService(?))', [$name, $namespaceName]);
		}
	}


	/**
	 * @param string $compiler
	 * @return string
	 */
	private function parseCompiler($compiler)
	{
		if (!isset($this->compilersAliases[$compiler]) && !class_exists($compiler)) {
			throw new InvalidArgumentException('Unknown compiler '. $compiler);
		}

		if (!class_exists($compiler)) {
			$compiler = $this->compilersAliases[$compiler];
		}

		return new PhpLiteral("new $compiler");
	}

}
