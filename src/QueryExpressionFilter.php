<?php

namespace Clue\JsonQuery;

use DomainException;

class QueryExpressionFilter implements Filter
{
    private $queryExpression;
    private $selectorSeparator = '.';

    public function __construct($queryExpression)
    {
        $this->queryExpression = $queryExpression;
    }

    public function doesMatch($data)
    {
        return $this->matchFilter($data, $this->queryExpression);
    }

    private function matchFilter($data, $filter)
    {
        if ($this->isObject($filter)) {
            return $this->matchAnd($data, $filter);
        } elseif (is_bool($filter)) {
            return $filter;
        } else {
            throw new DomainException('Invalid filter type');
        }
    }

    private function matchOr($data, $filter)
    {
        if ($this->isVector($filter)) {
            if (!$filter) {
                return true;
            }
            foreach ($filter as $element) {
                if ($this->matchFilter($data, $element)) {
                    return true;
                }
            }
        } elseif ($this->isObject($filter)) {
            foreach ($filter as $key => $value) {
                if ($this->matchValue($data, $key, $value)) {
                    return true;
                }
            }
        } else {
            throw new DomainException('Invalid data type for $or combinator');
        }

        return false;
    }

    private function matchAnd($data, $filter)
    {
        if ($this->isVector($filter)) {
            foreach ($filter as $element) {
                if (!$this->matchFilter($data, $element)) {
                    return false;
                }
            }
        } elseif ($this->isObject($filter)) {
            foreach ($filter as $key => $value) {
                if (!$this->matchValue($data, $key, $value)) {
                    return false;
                }
            }
        } else {
            throw new DomainException('Invalid data type for $and combinator');
        }

        return true;
    }

    private function matchValue($data, $column, $expectation)
    {
        if ($column === '$and') {
            return $this->matchAnd($data, $expectation);
        } elseif ($column === '$or') {
            return $this->matchOr($data, $expectation);
        } elseif ($column === '$not') {
            return !$this->matchAnd($data, $expectation);
        } elseif ($column[0] === '!') {
            return !$this->matchValue($data, substr($column, 1), $expectation);
        } elseif ($column[0] === '$') {
            throw new DomainException('Unknown combinator "' . $column . '"');
        }

        $expectedValue = $expectation;
        $comparator = '$is';

        if (is_array($expectedValue)) {
            if ($this->isVector($expectedValue)) {
                // list of possible values
                $comparator = '$in';
            } else {
                // custom comparator ('>', '<', ...)
                $comparator = key($expectedValue);
                $expectedValue = reset($expectedValue);

                if ($comparator === '$not') {
                    $comparator = ($this->isVector($expectedValue)) ? '!$in' : '!$is';
                }

                if ($comparator[0] === '!') {
                    $comparator = substr($comparator, 1);
                    return !$this->matchValue($data, $column, array($comparator => $expectedValue));
                }
            }
        }

        $actualValue = $this->fetchValue($data, $column);

        if ($comparator === '$is') {
            return ($actualValue === $expectedValue);
        } elseif ($comparator === '$in') {
            return in_array($actualValue, $expectedValue, true);
        } elseif ($comparator === '$lt') {
            return ($actualValue < $expectedValue);
        } elseif ($comparator === '$lte') {
            return ($actualValue <= $expectedValue);
        } elseif ($comparator === '$gt') {
            return ($actualValue > $expectedValue);
        } elseif ($comparator === '$gte') {
            return ($actualValue >= $expectedValue);
        } else {
            throw new DomainException('Unknown comparator "' . $comparator . '" given');
        }
    }

    private function fetchValue($data, $column)
    {
        // TODO: dotted notation
        return isset($data[$column]) ? $data[$column] : null;
    }

    private function isObject($value)
    {
        return (is_array($value) && ($value === array() || !isset($value[0])));
    }

    private function isVector($value)
    {
        return ($value === array() || (is_array($value) && isset($value[0])));
    }
}
