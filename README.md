# Fuel Module

[![Latest Stable Version](https://poser.pugx.org/indigophp/fuel-module/v/stable.png)](https://packagist.org/packages/indigophp/fuel-module)
[![Total Downloads](https://poser.pugx.org/indigophp/fuel-module/downloads.png)](https://packagist.org/packages/indigophp/fuel-module)
[![License](https://poser.pugx.org/indigophp/fuel-module/license.png)](https://packagist.org/packages/indigophp/fuel-module)

**This package is an extension to Fuel Module class.**


## Install

Via Composer

``` json
{
    "require": {
        "indigophp/fuel-module": "@stable"
    }
}
```

**Note:** This package cannot be used with `indigophp/fuel-core <1.0.3`.


## Usage

Load this package before any other package that uses one of the following: `Module`, `Request`, `Router`

Make sure that your extensions of these classes does not override them.

See [examples](https://github.com/indigophp/fuel-module/tree/develop/examples/) for custom modules.

**Note:** The modules are processed alphabetically (in the order they are loaded). The first controller found will be routed.

Depending on the examples, here are some scenarios:

1. Uri `moda` will route to `Moda\Controller_Modb` in module `moda`
2. Uri `moda/modb` will route to `Moda\Modb\Controller_Modb` in module `moda_modb`
3. Uri `moda/modb/modc` will route to `Moda\Modb\Controller_Modc` in module `moda_modb`


If `moda` contains a `Controller_Modb` (according to load order):

5. Uri `moda` will route to `Moda\Controller_Modb` in module `moda`
6. Uri `moda/modb` will route to `Moda\Controller_Modb` in module `moda`
7. Uri `moda/modb/modc` will route to `Moda\Controller_Modb` (action `modc`) in module `moda`


Fallbacks are processed in a reverse order:

8. Uri `moda/modb/fake` will route to `Moda\Modb\Controller_Modb` (action `fake`) in module `modb`


## Extending classes

You can extend 'Indigo\Fuel\Module' class to use your own namespace, URL prefix and default controller name. See `get_namespace`, `get_prefix` and `get_controller` functions.


## Contributing

Please see [CONTRIBUTING](https://github.com/indigophp/fuel-module/blob/develop/CONTRIBUTING.md) for details.


## Credits

- [Márk Sági-Kazár](https://github.com/sagikazarmark)
- [All Contributors](https://github.com/indigophp/fuel-module/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/indigophp/fuel-module/blob/develop/LICENSE) for more information.
