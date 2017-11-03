Brick\Std
=========

<img src="https://raw.githubusercontent.com/brick/brick/master/logo.png" alt="" align="left" height="64">

An attempt at a standard library for PHP.

[![Latest Stable Version](https://poser.pugx.org/brick/std/v/stable)](https://packagist.org/packages/brick/std)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](http://opensource.org/licenses/MIT)

Introduction
------------

The PHP internal functions are notoriously known for their inconsistency: inconsistent naming, inconsistent parameter order, inconsistent error handling: sometimes returning `false`, sometimes triggering an error, sometimes throwing an exception, and sometimes a mix of these.
The aim of this library is mainly to provide a consistent, OO wrapper around PHP native functions, that deals with inconsistencies internally to expose a cleaner API externally.
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

Overview
--------

### IO

File I/O functionality is provided via static methods in the [FileSystem](https://github.com/brick/std/blob/master/src/Io/FileSystem.php) class. All methods throw an [IoException](https://github.com/brick/std/blob/master/src/Io/IoException.php) on failure.

*The ultimate aim of this class would be to throw fine-grained exceptions for specific cases (file already exists, destination is a directory, etc.) but this would require to analyze PHP error messages, making the library fragile to changes, and/or call several internal filesystem functions in a row, making most of the operations non-atomic. Both approaches have potentially serious drawbacks. Ideas and comments welcome.*

Method list:

- `copy()` Copies a file.
- `move()` Moves a file or a directory.
- `delete()` Deletes a file.
- `createDirectory()` Creates a directory.
- `createDirectories()` Creates a directory by creating all nonexistent parent directories first.
- `exists()` Checks whether a file or directory exists.
- `isFile()` Checks whether the path points to a regular file.
- `isDirectory()` Checks whether the path points to a directory.
- `isSymbolicLink()` Checks whether the path points to a symbolic link.
- `createSymbolicLink()` Creates a symbolic link to a target.
- `createLink()` Creates a hard link to an existing file.
- `readSymbolicLink()` Returns the target of a symbolic link.
- `getRealPath()` Returns the canonicalized absolute pathname.
- `write()` Writes data to a file.
- `read()` Reads data from a file.

### JSON

JSON functionality is provided by [JsonEncoder](https://github.com/brick/std/blob/master/src/Json/JsonEncoder.php) and [JsonDecoder](https://github.com/brick/std/blob/master/src/Json/JsonDecoder.php). Options are set on the encoder/decoder instance, via explicit methods. If an error occurs, a [JsonException](https://github.com/brick/std/blob/master/src/Json/JsonException.php) is thrown.

Encoding:

```php
use Brick\Std\Json\JsonEncoder;

$encoder = new JsonEncoder();
$encoder->forceObject(true);

$encoder->encode(['Hello World']); // '{"0":"Hello World"}'
$encoder->encode(tmpfile()); // Brick\Std\Json\JsonException: Type is not supported
```

Decoding:

```php
use Brick\Std\Json\JsonDecoder;

$decoder = new JsonDecoder();
$decoder->decodeObjectAsArray(true);

$decoder->decode('{"hello":"world"}'); // ['hello' => 'world']
$decoder->decode('{hello}'); // Brick\Std\Json\JsonException: Syntax error
```
