<?php

/*
 * This file is part of the Fuel Module package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Autoloader::add_core_namespace('Indigo\\Fuel', true);

Autoloader::add_classes(array(
	'Indigo\\Fuel\\Module'  => __DIR__.'/classes/module.php',
	'Indigo\\Fuel\\Request' => __DIR__.'/classes/request.php',
	'Indigo\\Fuel\\Router'  => __DIR__.'/classes/router.php',
));
