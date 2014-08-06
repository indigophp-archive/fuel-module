<?php

/*
 * This file is part of the Fuel Module package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Fuel;

/**
 * Module class
 *
 * Overrides core module loading system to be able to use a better namespacing logic
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Module extends \Fuel\Core\Module
{
	/**
	 * {@inheritdoc}
	 */
	public static function load($module, $path = null)
	{
		if (is_array($module))
		{
			$result = true;
			foreach ($module as $mod => $path)
			{
				if (is_numeric($mod))
				{
					$mod = $path;
					$path = null;
				}
				$result = $result and static::load($mod, $path);
			}
			return $result;
		}


		if (static::loaded($module))
		{
			return;
		}

		// if no path is given, try to locate the module
		if ($path === null)
		{
			$paths = \Config::get('module_paths', array());

			if ( ! empty($paths))
			{
				foreach ($paths as $modpath)
				{
					if (is_dir($path = $modpath.strtolower($module).DS))
					{
						break;
					}
				}
			}
		}

		// make sure the path exists
		if ( ! is_dir($path))
		{
			throw new \ModuleNotFoundException("Module '$module' could not be found at '".\Fuel::clean_path($path)."'");
		}

		// determine the module namespace
		$ns = static::get_namespace($module);

		// add the namespace to the autoloader
		\Autoloader::add_namespaces(array(
			$ns  => $path.'classes'.DS,
		), true);

		static::load_routes($module, $path);

		static::$modules[$module] = $path;

		return true;
	}

	/**
	 * Lists all possible modules in all module paths
	 *
	 * @return array
	 */
	public static function all()
	{
		$paths = \Config::get('module_paths', array());

		$modules = array();

		foreach ($paths as $path)
		{
			$modules += array_map('basename', glob($path . '*', GLOB_ONLYDIR));
		}

		sort($modules);

		return $modules;
	}

	/**
	 * Returns the namespace for a module
	 *
	 * @param  string $module
	 * @return string
	 */
	public static function get_namespace($module)
	{
		return trim(str_replace('_', '\\', \Inflector::classify($module)), '\\');
	}

	/**
	 * Returns the default controller for a module
	 *
	 * @param  string $module
	 * @return string
	 */
	public static function get_controller($module)
	{
		$class = explode('_', \Inflector::classify($module));

		return end($class);
	}

	/**
	 * Returns the url prefix for a module
	 *
	 * @param  string $module
	 * @return string
	 */
	public static function get_prefix($module)
	{
		return trim(str_replace('_', '/', $module), '/');
	}

	/**
	 * Loads the routes for the module
	 *
	 * @param  string $module
	 * @param  string $path
	 */
	protected static function load_routes($module, $path)
	{
		// check if the module has routes
		if (is_file($path .= 'config/routes.php'))
		{
			// load and add the module routes
			$module_routes = \Fuel::load($path);
			$prefix = static::get_prefix($module);

			$prepped_routes = array();

			foreach($module_routes as $name => $_route)
			{
				if ($name === '_root_')
				{
					$name = $prefix;
				}
				elseif (strpos($name, $prefix.'/') !== 0 and $name != $prefix and $name !== '_404_')
				{
					$name = $prefix.'/'.$name;
				}

				$prepped_routes[$name] = $_route;
			};

			// update the loaded list of routes
			\Router::add($prepped_routes, null, true);
		}
	}
}
