# Carrooi/Assets

[![Build Status](https://travis-ci.org/Carrooi/Nette-Assets.svg?branch=master)](https://travis-ci.org/Carrooi/Nette-Assets)
[![Donate](http://b.repl.ca/v1/donate-PayPal-brightgreen.png)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WMC9CDEEYFJXC)

Simple modular assets system for Nette framework.

## Installation

```
$ composer require carrooi/assets
$ composer update
```

Then just enable nette extension in your config.neon:

```neon
extensions:
	assets: Carrooi\Assets\DI\AssetsExtension
```

## Configuration

```neon
extensions:
	assets: Carrooi\Assets\DI\AssetsExtension

assets:
	
	front:
		css:
			compiler: css
			paths:
				- %appDir%/../www/css/style.css
				- [mask: '*.css', from: %appDir%/../www/css/components]
				- [mask: '*.css', in: %appDir%/../www/css/core]
			target: %appDir%/../www/public/style.css
			publicPath: /public/style.css

		js:
			compiler: js
			paths:
				- %appDir%/../www/js
			target: %appDir%/../www/public/web.js
			publicPath: /public/web.js
```

Now we've got one namespace `front` with two resources `css` and `js`. You can create as many namespaces or resources as you want with any names.

Each resource needs its compiler, paths to compile and target built file with public path.

* **Compiler**: Currently just `css` or `js`. It can be also name of custom compiler class
* **Paths**: Names of files, directories or configurations for [nette/finder](https://github.com/nette/finder)
* **Target**: Path in file system to result file
* **PublicPath**: Path to built file accessible from browser

## Usage

```php

namespace App\Presenters;

use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter
{

	/**
	 * @var \Carrooi\Assets\UI\IAssetsControlFactory
	 * @inject
	 */
	public $assetsControlFactory;


	/**
	 * @return \Carrooi\Assets\UI\AssetsControl
	 */
	protected function createComponentAssets()
	{
		return $this->assetsControlFactory->create();
	}

}
```

```latte
{control assets, front, css}
```

That will combine all our registered css files into one and add it to your template via our component.

## Filters

```php
public function __construct(\Carrooi\Assets\Assets $assets)
{
	$resource = $assets->getResource('front', 'css');
	$resource->getCompiler()->addFileFilter(function($file) {
		return "/* Author: John Doe */\n". $file;
	});
}
```

This file filter on compiler will prepend author's name before each compiled file.

Or there are also filters for final built files.

```php
public function __construct(\Carrooi\Assets\Assets $assets)
{
	$resource = $assets->getResource('front', 'css');
	$resource->addFilter(function($file) {
		return "/* Built with Carrooi/Assets */\n". $file;
	});
}
```

This filter will add "Built with Carrooi/Assets" text to the beginning of target file.

## Add paths

```php
public function __construct(\Carrooi\Assets\Assets $assets)
{
	$resource = $assets->getResource('front', 'css');
	$resource->addPath(__DIR__. '/../../widget.css'); // just like in neon configuration
}
```

## CompilerExtension

Your compiler extensions can also implement `\Carrooi\Assets\DI\IAssetsProvider` interface for some additional 
configuration.

```php
namespace App\DI;

use Carrooi\Assets\DI\IAssetsProvider;
use Nette\DI\CompilerExtension;

class AppExtension extends CompilerExtension implements IAssetsProvider
{


	/**
	 * @return array
	 */
	public function getAssetsConfiguration()
	{
		return [
			'front' => [
				'css' => [
					'paths' => [
						__DIR__. '/../widget.css',
					],
				],
			],
		]
	}

}
```

## Changelog

* 1.0.0
	+ First version

* 1.0.1
	+ IAssetsProvider::getAssetsFiles() renamed to ::getAssetsConfiguration() [**BC Break**]
	
* 1.0.2
	+ Optimized dependencies
	+ Removed unused exceptions
	+ Optimized checking for files' modifications
