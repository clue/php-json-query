# clue/json-query [![Build Status](https://travis-ci.org/clue/php-json-query.svg?branch=master)](https://travis-ci.org/clue/php-json-query)

An implementation of the [JSON Query Language](https://github.com/clue/json-query-language) specification in PHP.

The [JSON Query Language](https://github.com/clue/json-query-language) specification is currently in draft mode.
This library implements the [v0.4 draft](https://github.com/clue/json-query-language/releases/tag/v0.4.0).

> Note: This project is in beta stage! Feel free to report any issues you encounter.

## Quickstart example

Once [installed](#install), you can use the following code to get started:

```php
$filter = new QueryExpressionFilter(array('age' => 20));

$filter->doesMatch(array('name' => 'Tester', 'age' => 20))); // true
$filter->doesMatch(array('name' => 'Tester', 'age' => 22))); // false
```

## Install

The recommended way to install this library is [through composer](http://getcomposer.org). [New to composer?](http://getcomposer.org/doc/00-intro.md)

```JSON
{
    "require": {
        "clue/json-query": "~0.3.0"
    }
}
```

## License

MIT
