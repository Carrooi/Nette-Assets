<?php

namespace Carrooi\Assets;

use Nette\Object;
use Nette\Utils\Finder;

/**
 *
 * @author David Kudera
 */
class Helpers extends Object
{


	/**
	 * @param array $paths
	 * @return array
	 */
	public static function expandFiles(array $paths)
	{
		$result = [];
		foreach ($paths as $path) {
			if (is_string($path)) {
				if (is_file($path)) {
					$result[] = self::normalize($path);
					continue;
				} elseif (is_dir($path)) {
					$path = [
						'mask' => '*',
						'from' => $path,
					];
				} else {
					throw new PathNotFoundException('Path '. $path. ' was not found.');
				}
			}

			if (!is_array($path)) {
				throw new InvalidArgumentException;
			}

			$finder = self::createFinder($path);
			foreach ($finder as $filePath => $file) {
				$result[] = self::normalize($filePath);
			}
		}

		return $result;
	}


	/**
	 * @param array $options
	 * @return \Nette\Utils\Finder
	 */
	private static function createFinder(array $options)
	{
		if (isset($options['mask']) && (isset($options['in']) || isset($options['from']))) {
			$finder = Finder::findFiles($options['mask']);
			if (isset($options['from'])) {
				if (!is_dir($options['from'])) {
					throw new DirectoryNotFoundException('Directory '. $options['from']. ' was not found.');
				}

				$finder->from($options['from']);
			}
			if (isset($options['in'])) {
				if (!is_dir($options['in'])) {
					throw new DirectoryNotFoundException('Directory '. $options['in']. ' was not found.');
				}

				$finder->in($options['in']);
			}
		} else {
			throw new InvalidStateException('Missing path mask or directory to search.');
		}

		return $finder;
	}


	/**
	 * @param string $path
	 * @return string
	 */
	public static function normalize($path)
	{
		$wrapper = '';

		preg_match('/^([a-z]+\:\/\/)?(.*)$/', $path, $match);
		if (count($match) === 3) {
			$wrapper = $match[1];
			$path = $match[2];
		}

		$root = ($path[0] === '/') ? '/' : '';

		$segments = explode('/', trim($path, '/'));
		$ret = [];

		foreach ($segments as $segment) {
			if (($segment == '.') || empty($segment)) {
				continue;
			}

			if ($segment == '..') {
				array_pop($ret);
			} else {
				array_push($ret, $segment);
			}
		}

		return $wrapper. $root. implode('/', $ret);
	}

}
