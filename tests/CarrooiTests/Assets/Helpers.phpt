<?php

/**
 * Test: Carrooi\Assets\Helpers
 *
 * @testCase CarrooiTests\Assets\HelpersTest
 * @author David Kudera
 */

namespace CarrooiTests\Assets\Authorization;

use Carrooi\Assets\Helpers;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__. '/../bootstrap.php';

/**
 *
 * @author David Kudera
 */
class HelpersTest extends TestCase
{


	/**
	 * @return array
	 */
	public function getNormalizePaths()
	{
		return [
			['/files/.././', '/'],
			['/files/css/./components/.././../packages.json', '/files/packages.json'],
			['file:///files/css/../packages.json', 'file:///files/packages.json']
		];
	}


	public function testExpandFiles()
	{
		$paths = [
			__DIR__. '/files/js',
			__DIR__. '/files/css/style.css',
			[
				'mask' => '*.css',
				'from' => __DIR__. '/files/css/components',
			],
			[
				'mask' => '*.css',
				'in' => __DIR__. '/files/css/core',
			]
		];

		$expect = [
			__DIR__. '/files/js/menu.js',
			__DIR__. '/files/js/web.js',
			__DIR__. '/files/css/style.css',
			__DIR__. '/files/css/components/widgets/favorite.css',
			__DIR__. '/files/css/components/footer.css',
			__DIR__. '/files/css/components/menu.css',
			__DIR__. '/files/css/core/mixins.css',
			__DIR__. '/files/css/core/variables.css',
		];

		$actual = Helpers::expandFiles($paths);

		sort($expect);
		sort($actual);

		Assert::same($expect, $actual);
	}


	/**
	 * @dataProvider getNormalizePaths
	 * @param string $actual
	 * @param string $expected
	 */
	public function testNormalize($actual, $expected)
	{
		Assert::same($expected, Helpers::normalize($actual));
	}

}


run(new HelpersTest);
