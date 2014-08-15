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
		$uri = implode('/', $match->segments);
		$info = false;
		$fallback = false;

		// check all modules
		// this might be a bigger performance hit since it is called twice
		foreach (\Module::all() as $module)
		{
			$prefix = \Module::get_prefix($module);

			// and route it if matches the uri
			if (strpos($uri, $prefix) === 0)
			{
				// load it
				// (makes sure the module is loaded if the match belongs to an unloaded module)
				\Module::load($module);

				$segments = explode('/', ltrim(substr($uri, strlen($prefix)), '/'));
				$namespace = \Module::get_namespace($module).'\\';

				// did we find a match
				if ($info = static::parse_segments($segments, $namespace))
				{
					// set active module
					$match->module = $module;

					// then stop looking
					break;
				}
				// or do we have a fallback
				elseif ($info = static::parse_segments($segments, $namespace, \Module::get_controller($module)))
				{
					// set active module
					$match->module = $module;

					// save it for later
					$fallback = $info;
					$info = false;
				}
			}
		}

		// process info or fall back
		if ($info or $info = $fallback or $info = static::parse_segments($match->segments, ''))
		{
			$match->controller = $info['controller'];
			$match->action = $info['action'];
			$match->method_params = $info['method_params'];

			return $match;
		}

		return null;
	}
}
