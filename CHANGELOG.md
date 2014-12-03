# CHANGELOG

This file is a manually maintained list of changes for each release. Feel free
to add your changes here when sending pull requests. Also send corrections if
you spot any mistakes.

## 0.3.0 (2014-12-03)

* Implement [json-query-language specification v0.4](https://github.com/clue/json-query-language/releases/tag/v0.4.0):

  * Feature: Add new `$contains` comparator
    (#10 by @clu and #6 by @ludmillaZ)

  * Feature: Support root matching
    (#13)

  * Feature: Support multiple operators
    (#14)

  * BC break: An empty `$or` combinator should never match
    (#9)

* Feature: Support matching objects in place of assoc arrays
  (#7)

* Refactor all operators into a map in order to ease extending
  (#12)

## 0.2.0 (2014-11-04)

* Feature: Support dotted notation for nested attributes
  (#5 by @maximkou)

* Remove: Remove boolean filters (dropped from spec v0.3)
  (#8)

## 0.1.0 (2014-08-21)

* First tagged release

## 0.0.0 (2014-08-20)

* Initial concept
