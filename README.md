Brick\Std
=========

<img src="https://raw.githubusercontent.com/brick/brick/master/logo.png" alt="" align="left" height="64">

An attempt at a standard library for PHP.

[![Build Status](https://secure.travis-ci.org/brick/std.svg?branch=master)](http://travis-ci.org/brick/std)
[![Coverage Status](https://coveralls.io/repos/brick/std/badge.svg?branch=master)](https://coveralls.io/r/brick/std?branch=master)
[![Latest Stable Version](https://poser.pugx.org/brick/std/v/stable)](https://packagist.org/packages/brick/std)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](http://opensource.org/licenses/MIT)

Introduction
------------

The PHP internal functions are notorious for their inconsistency: inconsistent naming, inconsistent parameter order, inconsistent error handling: sometimes returning `false`, sometimes triggering an error, sometimes throwing an exception, and sometimes a mix of these.
The aim of this library is mainly to provide a consistent, object-oriented wrapper around PHP native functions, that deals with inconsistencies internally to expose a cleaner API externally.
Hopefully PHP will do this job one day; in the meantime, this project is a humble attempt to fill the gap.

The library will start small. Functionality will be added as needs arise. Contributions are welcome.

Project status & release process
--------------------------------

The current releases are numbered `0.x.y`. When a non-breaking change is introduced (adding new methods, optimizing existing code, etc.), `y` is incremented.

**When a breaking change is introduced, a new `0.x` version cycle is always started.**

It is therefore safe to lock your project to a given release cycle, such as `0.1.*`.

If you need to upgrade to a newer release cycle, check the [release history](https://github.com/brick/std/releases)
for a list of changes introduced by each further `0.x.0` version.

Installation
------------

This library is installable via [Composer](https://getcomposer.org/):

```bash
composer require brick/std
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

### Iterator

The library ships with two handy iterator for CSV files:

#### CsvFileIterator

This iterator iterates over a CSV file, and returns an indexed array by default:

```php
use Brick\Std\Iterator\CsvFileIterator;

// 1,Bob,New York
// 2,John,Los Angeles
$users = new CsvFileIterator('users.csv');

foreach ($users as [$id, $name, $city]) {
    // ...
}
```

It can also read the first line of the file that contains column names, and use them to return an associative array:

```php
use Brick\Std\Iterator\CsvFileIterator;

// id,name,city
// 1,Bob,New York
// 2,John,Los Angeles
$users = new CsvFileIterator('users.csv', true);

foreach ($users as $user) {
    // $user['id'], $user['name'], $user['city']
}
```

Delimiter, enclosure and escape characters can be provided to the constructor.

#### CsvJsonFileIterator

This iterator iterates over a CSV file whose fields are JSON-encoded:

```php
use Brick\Std\Iterator\CsvJsonFileIterator;

// 1,"Bob",["John","Mike"]
// 2,"John",["Bob","Brad"]
$users = new CsvJsonFileIterator('users.csv');

foreach ($users as [$id, $name, $friends]) {
    // $id is an int
    // $name is a string
    // $friends is an array
}
```

The JSON-encoded fields must not contain newline characters.

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
