<?php

use Clue\JsonQuery\QueryExpressionFilter;

class QueryExpressionFilterTest extends TestCase
{
    public function testAttributeValue()
    {
        $filter = new QueryExpressionFilter(array(
            'id' => 100
        ));

        $this->assertTrue($filter->doesMatch(array(
            'id' => 100
        )));

        $this->assertFalse($filter->doesMatch(array(
            'id' => 200
        )));

        return $filter;
    }

    /**
     * @depends testAttributeValue
     * @param QueryExpressionFilter $filter
     */
    public function testAttributeTypeMatters(QueryExpressionFilter $filter)
    {
        $this->assertFalse($filter->doesMatch(array(
            'id' => '100'
        )));
    }

    /**
     * @depends testAttributeValue
     * @param QueryExpressionFilter $filter
     */
    public function testAttributeMissingDoesNotMatch(QueryExpressionFilter $filter)
    {
        $this->assertFalse($filter->doesMatch(array(
            'name' => 'test'
        )));
    }

    public function testNestedAttributeValue()
    {
        $filter = new QueryExpressionFilter(array(
            'nested.attribute' => 250
        ));

        $this->assertFalse($filter->doesMatch(array()));
        $this->assertFalse($filter->doesMatch((object)array()));

        $this->assertTrue($filter->doesMatch(array(
            'nested' => array(
                'attribute' => 250
            )
        )));

        $this->assertTrue($filter->doesMatch((object)array(
            'nested' => (object)array(
                'attribute' => 250
            )
        )));

        $this->assertFalse($filter->doesMatch(array(
            'nested' => array(
                'attribute' => 300
            )
        )));
    }

    public function testAttributeListOfValues()
    {
        $filter = new QueryExpressionFilter(array(
            'id' => array(100, 200, 300)
        ));

        $this->assertTrue($filter->doesMatch(array('id' => 100)));
        $this->assertFalse($filter->doesMatch(array('id' => 400)));
    }

    public function testMultipleAttributesMatchLikeAnd()
    {
        $filter = new QueryExpressionFilter(array(
            'id' => 100,
            'name' => 'Test',
        ));

        $this->assertTrue($filter->doesMatch(array(
            'id' => 100,
            'name' => 'Test'
        )));

        $this->assertTrue($filter->doesMatch(array(
            'id' => 100,
            'name' => 'Test',
            'age' => 20
        )));

        $this->assertFalse($filter->doesMatch(array(
            'id' => 100,
            'name' => 'Other'
        )));
    }

    public function testAndList()
    {
        $filter = new QueryExpressionFilter(array(
            '$and' => array(
                array(
                    'id' => 100
                ),
                array(
                    'name' => 'Test'
                )
            )
        ));

        $this->assertTrue($filter->doesMatch(array(
            'id' => 100,
            'name' => 'Test'
        )));

        $this->assertFalse($filter->doesMatch(array(
            'id' => 100,
            'name' => 'invalid'
        )));
    }

    public function testAndObject()
    {
        $filter = new QueryExpressionFilter(array(
            '$and' => array(
                'id' => 100,
                'name' => 'Test'
            )
        ));

        $this->assertTrue($filter->doesMatch(array(
            'id' => 100,
            'name' => 'Test'
        )));

        $this->assertFalse($filter->doesMatch(array(
            'id' => 100,
            'name' => 'invalid'
        )));
    }

    public function testAndStdObject()
    {
        $filter = new QueryExpressionFilter((object)array(
            '$and' => (object)array(
                'id' => 100,
                'name' => 'Test'
            )
        ));

        $this->assertTrue($filter->doesMatch((object)array(
            'id' => 100,
            'name' => 'Test'
        )));

        $this->assertFalse($filter->doesMatch((object)array(
            'id' => 100,
            'name' => 'invalid'
        )));
    }

    public function testOrList()
    {
        $filter = new QueryExpressionFilter(array(
            '$or' => array(
                array(
                    'id' => 100
                ),
                array(
                    'id' => 200
                )
            )
        ));

        $this->assertTrue($filter->doesMatch(array(
            'id' => 100,
        )));

        $this->assertTrue($filter->doesMatch(array(
            'id' => 200,
        )));

        $this->assertFalse($filter->doesMatch(array(
            'id' => 300,
        )));
    }

    public function testOrObject()
    {
        $filter = new QueryExpressionFilter(array(
            '$or' => array(
                'id' => 100,
                'name' => 'Test'
            )
        ));

        $this->assertTrue($filter->doesMatch(array(
            'id' => 100,
        )));

        $this->assertTrue($filter->doesMatch(array(
            'name' => 'Test',
        )));

        $this->assertFalse($filter->doesMatch(array(
            'id' => 300,
        )));
    }

    public function testEmptyListAlwaysMatches()
    {
        $filter = new QueryExpressionFilter(array());

        $this->assertTrue($filter->doesMatch(array('id' => 100)));
    }

    public function testEmptyAndAlwaysMatches()
    {
        $filter = new QueryExpressionFilter(array(
            '$and' => array()
        ));

        $this->assertTrue($filter->doesMatch(array('id' => 100)));
    }

    public function testEmptyOrAlwaysMatches()
    {
        $filter = new QueryExpressionFilter(array(
            '$or' => array()
        ));

        $this->assertTrue($filter->doesMatch(array('id' => 100)));
    }

    public function testEmptyOrObjectAlwaysMatches()
    {
        $filter = new QueryExpressionFilter((object)array(
            '$or' => (object)array()
        ));

        $this->assertTrue($filter->doesMatch((object)array('id' => 100)));
    }

    public function testEmptyNotNeverMatches()
    {
        $filter = new QueryExpressionFilter(array(
            '$not' => array()
        ));

        $this->assertFalse($filter->doesMatch(array('id' => 100)));
    }

    public function testEmptyNotAndNeverMatches()
    {
        $filter = new QueryExpressionFilter(array(
            '!$and' => array()
        ));

        $this->assertFalse($filter->doesMatch(array('id' => 100)));
    }

    public function testAttributeNotValue()
    {
        $filter = new QueryExpressionFilter(array(
            'id' => array('$not' => 100)
        ));

        $this->assertFalse($filter->doesMatch(array('id' => 100)));
        $this->assertTrue($filter->doesMatch(array('id' => 300)));
    }

    public function testAttributeLtValue()
    {
        $filter = new QueryExpressionFilter(array(
            'id' => array('$lt' => 100)
        ));

        $this->assertFalse($filter->doesMatch(array('id' => 100)));
        $this->assertTrue($filter->doesMatch(array('id' => 99)));
    }

    public function testAttributeLteValue()
    {
        $filter = new QueryExpressionFilter(array(
            'id' => array('$lte' => 100)
        ));

        $this->assertFalse($filter->doesMatch(array('id' => 101)));
        $this->assertTrue($filter->doesMatch(array('id' => 100)));
    }

    public function testAttributeGtValue()
    {
        $filter = new QueryExpressionFilter(array(
            'id' => array('$gt' => 100)
        ));

        $this->assertFalse($filter->doesMatch(array('id' => 100)));
        $this->assertTrue($filter->doesMatch(array('id' => 101)));
    }

    public function testAttributeGteValue()
    {
        $filter = new QueryExpressionFilter(array(
            'id' => array('$gte' => 100)
        ));

        $this->assertFalse($filter->doesMatch(array('id' => 99)));
        $this->assertTrue($filter->doesMatch(array('id' => 100)));
    }

    public function testAttributeNotIsValue()
    {
        $filter = new QueryExpressionFilter(array(
            'id' => array('!$is' => 100)
        ));

        $this->assertFalse($filter->doesMatch(array('id' => 100)));
        $this->assertTrue($filter->doesMatch(array('id' => 300)));
    }

    public function testAttributeNotInList()
    {
        $filter = new QueryExpressionFilter(array(
            'id' => array('!$in' => array(100, 200))
        ));

        $this->assertFalse($filter->doesMatch(array('id' => 100)));
        $this->assertTrue($filter->doesMatch(array('id' => 300)));
    }

    public function testAttributeNotList()
    {
        $filter = new QueryExpressionFilter(array(
            'id' => array('$not' => array(100, 200))
        ));

        $this->assertFalse($filter->doesMatch(array('id' => 100)));
        $this->assertTrue($filter->doesMatch(array('id' => 300)));
    }

    public function testAttributeContainsString()
    {
        $filter = new QueryExpressionFilter(array(
            'key' => array(
                '$contains' => 'value'
            )
        ));

        $this->assertTrue($filter->doesMatch(array(
            'key' => 'the value exist'
        )));
        $this->assertFalse($filter->doesMatch(array(
            'key' => 'empty'
        )));
    }

    public function testAttributeContainsArray()
    {
        $filter = new QueryExpressionFilter(array(
            'key' => array(
                '$contains' => 'value'
            )
        ));

        $this->assertTrue($filter->doesMatch(array(
            'key' => array('the', 'value', 'exists')
        )));
        $this->assertFalse($filter->doesMatch(array(
            'key' => array('not', 'present')
        )));
    }

    public function testAttributeContainsAssoc()
    {
        $filter = new QueryExpressionFilter(array(
            'key' => array(
                '$contains' => 'value'
            )
        ));

        $this->assertTrue($filter->doesMatch(array(
            'key' => array(
                'value' => 123
            )
        )));
        $this->assertFalse($filter->doesMatch(array(
            'key' => array(
                'not' => 'present'
            )
        )));
    }

    public function testAttributeContainsObject()
    {
        $filter = new QueryExpressionFilter(array(
            'key' => array(
                '$contains' => 'value'
            )
        ));

        $this->assertTrue($filter->doesMatch((object)array(
            'key' => (object)array(
                'value' => 123
            )
        )));
        $this->assertFalse($filter->doesMatch((object)array(
            'key' => (object)array(
                'not' => 'present'
            )
        )));
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidOperator()
    {
        $filter = new QueryExpressionFilter(array(
            'id' => array('$invalid' => 100)
        ));

        $filter->doesMatch(array('id' => 100));
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidCombinator()
    {
        $filter = new QueryExpressionFilter(array(
            '$invalid' => array()
        ));

        $filter->doesMatch(array('id' => 100));
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidFilter()
    {
        $filter = new QueryExpressionFilter('invalid');

        $filter->doesMatch(array('id' => 100));
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidAnd()
    {
        $filter = new QueryExpressionFilter(array(
            '$and' => 'invalid'
        ));

        $filter->doesMatch(array('id' => 100));
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidOr()
    {
        $filter = new QueryExpressionFilter(array(
            '$or' => 'invalid'
        ));

        $filter->doesMatch(array('id' => 100));
    }

    public function testAttributeContains()
    {
        $filter = new QueryExpressionFilter(array(
            'name' => array(
                '$contains' => 'Fried'
            )
        ));

        $this->assertFalse($filter->doesMatch(array(
            'id' => 100,
            'name' => 'Smith, George'
        )));
        $this->assertTrue($filter->doesMatch(array(
            'id' => 300,
            'name' => 'Smith, Friedrich'
        )));
    }

    /**
     * @dataProvider dpFetchValue
     */
    public function testFetchValue($data, $fetch, $expected)
    {
        $filter = new QueryExpressionFilter(array());
        $method = new \ReflectionMethod($filter, 'fetchValue');
        $method->setAccessible(true);

        $this->assertEquals(
            $expected,
            $method->invoke($filter, $data, $fetch)
        );
    }

    public function dpFetchValue()
    {
        return array(
            array(
                array('id' => 1),
                'id',
                1
            ),
            array(
                array(
                    'org' => array(
                        'peoples' => array(
                            'john' => array(
                                'weight' => 80
                            )
                        )
                    )
                ),
                'org.peoples.john',
                array(
                    'weight' => 80
                )
            ),
            array(
                array(
                    'org' => array(
                        'peoples' => array(
                            'john' => array(
                                'weight' => 80
                            )
                        ),
                        'computers' => array(
                            'pc' => 'Intel',
                            'macbook' => 'Macbook Pro'
                        )
                    )
                ),
                'org.computers.pc',
                'Intel'
            ),
            array(
                array('id' => 1),
                'unexpected',
                null
            ),
        );
    }
}
