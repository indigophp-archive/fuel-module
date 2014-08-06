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
}
