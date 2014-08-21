# clue/json-query [![Build Status](https://travis-ci.org/clue/php-json-query.svg?branch=master)](https://travis-ci.org/clue/php-json-query)

An implementation of the *experimental* [JSON Query Language](https://github.com/clue/json-query-language) in PHP.

> Note: This project is in early alpha stage! Feel free to report any issues you encounter.

## Quickstart example

Once [installed](#install), you can use the following code to get started:

```php
$filter = new QueryExpressionFilter(array('age' => 20));

$filter->doesMatch(array('name' => 'Tester', 'age' => 20))); // true
$filter->doesMatch(array('name' => 'Tester', 'age' => 22))); // false
```

See also the [examples](examples).

## Install

The recommended way to install this library is [through composer](http://getcomposer.org). [New to composer?](http://getcomposer.org/doc/00-intro.md)

```JSON
{
    "require": {
        "clue/json-query": "dev-master"
    }
}
```

## License

MIT
