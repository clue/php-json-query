<?php

namespace Clue\JsonQuery;

use DomainException;

class QueryExpressionFilter implements Filter
{
    private $queryExpression;
    private $selectorSeparator = '.';

    private $comparators = array();

    public function __construct($queryExpression)
    {
        $this->queryExpression = $queryExpression;

        $that = $this;
        $this->comparators = array(
            '$is' => function ($actualValue, $expectedValue) {
                return ($actualValue === $expectedValue);
            },
            '$in' => function ($actualValue, $expectedValue) {
                return in_array($actualValue, $expectedValue, true);
            },
            '$contains' => function ($actualValue, $expectedValue) use ($that) {
                if ($that->isObject($actualValue)) {
                    if (is_object($actualValue)) {
                        return property_exists($actualValue, $expectedValue);
                    } else {
                        return array_key_exists($expectedValue, $actualValue);
                    }
                } elseif ($that->isVector($actualValue)) {
                    return in_array($expectedValue, $actualValue, true);
                } else {
                    return (strpos($actualValue, $expectedValue) !== false);
                }
            },
            '$lt' => function ($actualValue, $expectedValue) {
                return ($actualValue < $expectedValue);
            },
            '$lte' => function ($actualValue, $expectedValue) {
                return ($actualValue <= $expectedValue);
            },
            '$gt' => function ($actualValue, $expectedValue) {
                return ($actualValue > $expectedValue);
            },
            '$gte' => function ($actualValue, $expectedValue) {
                return ($actualValue >= $expectedValue);
            },
            '$not' => function ($actualValue, $expectedValue) use ($that) {
                return !$that->matchComparator($actualValue, $that->isVector($expectedValue) ? '$in' : '$is', $expectedValue);
            },
        );
    }

    public function doesMatch($data)
    {
        return $this->matchFilter($data, $this->queryExpression);
    }

    private function matchFilter($data, $filter)
    {
        if ($this->isObject($filter)) {
            return $this->matchAnd($data, $filter);
        } else {
            throw new DomainException('Invalid filter type');
        }
    }

    private function matchOr($data, $filter)
    {
        if ($this->isVector($filter)) {
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
            return $this->matchComparator($data, $column, $expectation);
        }

        if ($this->isVector($expectation)) {
            // L2 simple list matching
            $expectation = array('$in' => $expectation);
        } elseif (!$this->isObject($expectation)) {
            // L2 simple scalar matching
            $expectation = array('$is' => $expectation);
        }

        $actualValue = $this->fetchValue($data, $column);

        foreach ($expectation as $comparator => $expectedValue) {
            $ret = $this->matchComparator($actualValue, $comparator, $expectedValue);

            if (!$ret) {
                return false;
            }
        }

        return true;
    }

    private function fetchValue($data, $column)
    {
        $path = explode($this->selectorSeparator, $column);

        foreach ($path as $field) {
            if (is_array($data) && isset($data[$field])) {
                $data = $data[$field];
            } elseif (is_object($data) && isset($data->$field)) {
                $data = $data->$field;
            } else {
                return null;
            }
        }

        return $data;
    }

    /** @internal */
    public function matchComparator($actualValue, $comparator, $expectedValue)
    {
        $negate = false;
        while ($comparator[0] === '!') {
            $negate = !$negate;
            $comparator = substr($comparator, 1);
        }

        if (!isset($this->comparators[$comparator])) {
            throw new DomainException('Unknown comparator "' . $comparator . '" given');
        }

        return $this->comparators[$comparator]($actualValue, $expectedValue) XOR $negate;
    }

    /** @internal */
    public function isObject($value)
    {
        return (is_object($value) || (is_array($value) && ($value === array() || !isset($value[0]))));
    }

    /** @internal */
    public function isVector($value)
    {
        return ($value === array() || (is_array($value) && isset($value[0])));
    }
}
