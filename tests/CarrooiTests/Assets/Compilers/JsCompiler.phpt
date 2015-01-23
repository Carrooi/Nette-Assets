<?php

/**
 * Test: Carrooi\Assets\Compilers\JsCompiler
 *
 * @testCase CarrooiTests\Assets\Compilers\JsCompilerTest
 * @author David Kudera
 */

namespace CarrooiTests\Assets\Compilers;

use Carrooi\Assets\Compilers\JsCompiler;
use Tester\Assert;
use Tester\FileMock;
use Tester\TestCase;

require_once __DIR__. '/../../bootstrap.php';

/**
 *
 * @author David Kudera
 */
class JsCompilerTest extends TestCase
{


	/** @var \Carrooi\Assets\Compilers\JsCompiler */
	private $compiler;


	public function setUp()
	{
		$this->compiler = new JsCompiler;
	}


	public function testCompile()
	{
		$files = [
			FileMock::create("alert('hello');\n"),
			FileMock::create("$(function() {\n\talert('ready');\n});\n"),
		];

		$js = $this->compiler->compile($files, FileMock::create(''));

		Assert::same("(function() {\n\talert('hello');\n\n}).call();\n(function() {\n\t$(function() {\n\t\talert('ready');\n\t});\n\n}).call();\n", $js);
	}


	public function testCompile_fileFilter()
	{
		$files = [
			FileMock::create("alert('hello');\n"),
			FileMock::create("$(function() {\n\talert('ready');\n});\n"),
		];

		$this->compiler->addFileFilter(function($js) {
			return str_replace('hello', 'hello world', $js);
		});

		$js = $this->compiler->compile($files, FileMock::create(''));

		Assert::same("(function() {\n\talert('hello world');\n\n}).call();\n(function() {\n\t$(function() {\n\t\talert('ready');\n\t});\n\n}).call();\n", $js);
	}


	public function testCreateHtml()
	{
		Assert::same('<script type="text/javascript" src="web.js"></script>', $this->compiler->createHtml('web.js'));
	}

}


run(new JsCompilerTest);
