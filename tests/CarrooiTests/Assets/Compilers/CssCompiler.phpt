<?php

/**
 * Test: Carrooi\Assets\Compilers\CssCompiler
 *
 * @testCase CarrooiTests\Assets\Compilers\CssCompilerTest
 * @author David Kudera
 */

namespace CarrooiTests\Assets\Compilers;

use Carrooi\Assets\Compilers\CssCompiler;
use Tester\Assert;
use Tester\FileMock;
use Tester\TestCase;

require_once __DIR__. '/../../bootstrap.php';

/**
 *
 * @author David Kudera
 */
class CssCompilerTest extends TestCase
{


	/** @var \Carrooi\Assets\Compilers\CssCompiler */
	private $compiler;


	public function setUp()
	{
		$this->compiler = new CssCompiler;
	}


	public function testCompile()
	{
		$files = [
			FileMock::create('body {color: red;}'),
			FileMock::create('h1 {color: black;}'),
		];

		$css = $this->compiler->compile($files, FileMock::create(''));

		Assert::same("body {color: red;}\nh1 {color: black;}\n", $css);
	}


	public function testCompile_fileFilter()
	{
		$files = [
			FileMock::create('body {color: red;}'),
			FileMock::create('h1 {color: black;}'),
		];

		$this->compiler->addFileFilter(function($css) {
			return str_replace('color', 'background-color', $css);
		});

		$css = $this->compiler->compile($files, FileMock::create(''));

		Assert::same("body {background-color: red;}\nh1 {background-color: black;}\n", $css);
	}


	public function testCreateHtml()
	{
		Assert::same('<link href="style.css" rel="stylesheet" type="text/css">', $this->compiler->createHtml('style.css'));
	}

}


run(new CssCompilerTest);
