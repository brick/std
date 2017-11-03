Brick\Std
=========

<img src="https://raw.githubusercontent.com/brick/brick/master/logo.png" alt="" align="left" height="64">

An attempt at a standard library for PHP.

[![Latest Stable Version](https://poser.pugx.org/brick/std/v/stable)](https://packagist.org/packages/brick/std)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](http://opensource.org/licenses/MIT)

Introduction
------------

The PHP internal functions are notoriously known for their inconsistency: inconsistent naming, inconsistent parameter order, inconsistent error handling: sometimes returning `false`, sometimes triggering an error, sometimes throwing an exception, and sometimes a mix of these.
The aim of this library is mainly to provide a consistent, OOP wrapper around PHP native functions, that deals with inconsistencies internally to expose a cleaner API externally.
Hopefully PHP will do this job one day; in the meantime, this project is a humble attempt to fill the gap.

The library will start small. Functionality will be added as needs arise. Contributions are welcome.

Installation
------------

This library is installable via [Composer](https://getcomposer.org/).
Just define the following requirement in your `composer.json` file:

```json
{
    "require": {
        "brick/std": "dev-master"
    }
}
```

Requirements
------------

This library requires PHP 7.1 or later.
