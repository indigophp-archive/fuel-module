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
 * Request class
 *
 * Removes incorrect module usage from Request
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Request extends \Fuel\Core\Request
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct($uri, $route = true, $method = null)
	{
		$this->uri = new \Uri($uri);
		$uri = $this->uri->get();
		$this->method = $method ?: \Input::method();

		logger(\Fuel::L_INFO, 'Creating a new '.(static::$main==null?'main':'HMVC').' Request with URI = "'.$this->uri->get().'"', __METHOD__);

		// check if a module was requested
		foreach (\Module::all() as $module)
		{
			$prefix = \Module::get_prefix($module);

			if (strpos($uri, $prefix) === 0)
			{
				// and load it if yes
				\Module::load($module);
			}
		}

		$this->route = \Router::process($this, $route);

		if ( ! $this->route)
		{
			return;
		}

		$this->module = $this->route->module;
		$this->controller = $this->route->controller;
		$this->action = $this->route->action;
		$this->method_params = $this->route->method_params;
		$this->named_params = $this->route->named_params;

		if ($this->route->module !== null)
		{
			$this->add_path(\Module::exists($this->module));
		}
	}
}
