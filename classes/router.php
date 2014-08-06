<?php

/*
 * This file is part of the Indigo Module package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Fuel;

/**
 * Router class
 *
 * Overrides module routing
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Router extends \Fuel\Core\Router
{
	/**
	 * {@inheritdoc}
	 */
	protected static function parse_match($match)
	{
		$namespace = '';
		$segments = $match->segments;
		$module = false;
		$info = false;

		// check the loaded modules
		foreach (\Module::loaded() as $module => $path)
		{
			$prefix = \Module::get_prefix($module);

			// and route it if matches the uri
			if (strpos($match->translation, $prefix) === 0)
			{
				$segments = explode('/', ltrim(substr($match->translation, strlen($prefix)), '/'));
				$namespace = \Module::get_namespace($module).'\\';
				$match->module = $module;

				// did we find a match
				if ($info = static::parse_segments($segments, $namespace))
				{
					// then stop looking
					break;
				}
			}
		}

		$controller = \Module::get_controller($module);

		// process info or fall back
		if ($info or $info = static::parse_segments($segments, $namespace, $controller))
		{
			$match->controller = $info['controller'];
			$match->action = $info['action'];
			$match->method_params = $info['method_params'];

			return $match;
		}

		return null;
	}

	/**
	 * {@inheritdoc}
	 */
	// protected static function parse_segments($segments, $namespace = '', $module = false)
	// {
	// 	$temp_segments = $segments;
	// 	$prefix = static::get_prefix();

	// 	foreach (array_reverse($segments, true) as $key => $segment)
	// 	{
	// 		// determine which classes to check. First, all underscores, or all namespaced
	// 		$classes = array(
	// 			$namespace.$prefix.\Inflector::words_to_upper(implode(substr($prefix,-1,1), $temp_segments), substr($prefix,-1,1)),
	// 		);

	// 		// if we're namespacing, check a hybrid version too
	// 		$classes[] = $namespace.$prefix.\Inflector::words_to_upper(implode('_', $temp_segments));

	// 		array_pop($temp_segments);

	// 		foreach ($classes as $class)
	// 		{
	// 			if (static::check_class($class))
	// 			{
	// 				return array(
	// 					'controller'    => $class,
	// 					'action'        => isset($segments[$key + 1]) ? $segments[$key + 1] : null,
	// 					'method_params' => array_slice($segments, $key + 2),
	// 				);
	// 			}
	// 		}
	// 	}

	// 	return false;
	// }
}
