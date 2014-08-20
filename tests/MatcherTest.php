<?php

use Clue\JsonQuery\Matcher;

class MatcherTest extends TestCase
{
    public function testAttributeValue()
    {
        $matcher = new Matcher(array(
            'id' => 100
        ));

        $this->assertTrue($matcher->match(array(
            'id' => 100
        )));

        $this->assertFalse($matcher->match(array(
            'id' => 200
        )));

        return $matcher;
    }

    /**
     * @depends testAttributeValue
     * @param Matcher $matcher
     */
    public function testAttributeTypeMatters(Matcher $matcher)
    {
        $this->assertFalse($matcher->match(array(
            'id' => '100'
        )));
    }

    /**
     * @depends testAttributeValue
     * @param Matcher $matcher
     */
    public function testAttributeMissingDoesNotMatch(Matcher $matcher)
    {
        $this->assertFalse($matcher->match(array(
            'name' => 'test'
        )));
    }

    public function testAttributeListOfValues()
    {
        $matcher = new Matcher(array(
            'id' => array(100, 200, 300)
        ));

        $this->assertTrue($matcher->match(array('id' => 100)));
        $this->assertFalse($matcher->match(array('id' => 400)));
    }

    public function testMultipleAttributesMatchLikeAnd()
    {
        $matcher = new Matcher(array(
            'id' => 100,
            'name' => 'Test',
        ));

        $this->assertTrue($matcher->match(array(
            'id' => 100,
            'name' => 'Test'
        )));

        $this->assertTrue($matcher->match(array(
            'id' => 100,
            'name' => 'Test',
            'age' => 20
        )));

        $this->assertFalse($matcher->match(array(
            'id' => 100,
            'name' => 'Other'
        )));
    }

    public function testAndList()
    {
        $matcher = new Matcher(array(
            '$and' => array(
                array(
                    'id' => 100
                ),
                array(
                    'name' => 'Test'
                )
            )
        ));

        $this->assertTrue($matcher->match(array(
            'id' => 100,
            'name' => 'Test'
        )));

        $this->assertFalse($matcher->match(array(
            'id' => 100,
            'name' => 'invalid'
        )));
    }

    public function testAndObject()
    {
        $matcher = new Matcher(array(
            '$and' => array(
                'id' => 100,
                'name' => 'Test'
            )
        ));

        $this->assertTrue($matcher->match(array(
            'id' => 100,
            'name' => 'Test'
        )));

        $this->assertFalse($matcher->match(array(
            'id' => 100,
            'name' => 'invalid'
        )));
    }

    public function testOrList()
    {
        $matcher = new Matcher(array(
            '$or' => array(
                array(
                    'id' => 100
                ),
                array(
                    'id' => 200
                )
            )
        ));

        $this->assertTrue($matcher->match(array(
            'id' => 100,
        )));

        $this->assertTrue($matcher->match(array(
            'id' => 200,
        )));

        $this->assertFalse($matcher->match(array(
            'id' => 300,
        )));
    }

    public function testOrObject()
    {
        $matcher = new Matcher(array(
            '$or' => array(
                'id' => 100,
                'name' => 'Test'
            )
        ));

        $this->assertTrue($matcher->match(array(
            'id' => 100,
        )));

        $this->assertTrue($matcher->match(array(
            'name' => 'Test',
        )));

        $this->assertFalse($matcher->match(array(
            'id' => 300,
        )));
    }

    public function testEmptyListAlwaysMatches()
    {
        $matcher = new Matcher(array());

        $this->assertTrue($matcher->match(array('id' => 100)));
    }

    public function testEmptyAndAlwaysMatches()
    {
        $matcher = new Matcher(array(
            '$and' => array()
        ));

        $this->assertTrue($matcher->match(array('id' => 100)));
    }

    public function testEmptyOrAlwaysMatches()
    {
        $matcher = new Matcher(array(
            '$or' => array()
        ));

        $this->assertTrue($matcher->match(array('id' => 100)));
    }

    public function testEmptyNotNeverMatches()
    {
        $matcher = new Matcher(array(
            '$not' => array()
        ));

        $this->assertFalse($matcher->match(array('id' => 100)));
    }

    public function testEmptyNotAndNeverMatches()
    {
        $matcher = new Matcher(array(
            '!$and' => array()
        ));

        $this->assertFalse($matcher->match(array('id' => 100)));
    }

    public function testTrueAlwaysMatches()
    {
        $matcher = new Matcher(true);

        $this->assertTrue($matcher->match(array('id' => 100)));
    }

    public function testFalseNeverMatches()
    {
        $matcher = new Matcher(false);

        $this->assertFalse($matcher->match(array('id' => 100)));
    }

    public function testAttributeNotValue()
    {
        $matcher = new Matcher(array(
            'id' => array('$not' => 100)
        ));

        $this->assertFalse($matcher->match(array('id' => 100)));
        $this->assertTrue($matcher->match(array('id' => 300)));
    }

    public function testAttributeLtValue()
    {
        $matcher = new Matcher(array(
            'id' => array('$lt' => 100)
        ));

        $this->assertFalse($matcher->match(array('id' => 100)));
        $this->assertTrue($matcher->match(array('id' => 99)));
    }

    public function testAttributeLteValue()
    {
        $matcher = new Matcher(array(
            'id' => array('$lte' => 100)
        ));

        $this->assertFalse($matcher->match(array('id' => 101)));
        $this->assertTrue($matcher->match(array('id' => 100)));
    }

    public function testAttributeGtValue()
    {
        $matcher = new Matcher(array(
            'id' => array('$gt' => 100)
        ));

        $this->assertFalse($matcher->match(array('id' => 100)));
        $this->assertTrue($matcher->match(array('id' => 101)));
    }

    public function testAttributeGteValue()
    {
        $matcher = new Matcher(array(
            'id' => array('$gte' => 100)
        ));

        $this->assertFalse($matcher->match(array('id' => 99)));
        $this->assertTrue($matcher->match(array('id' => 100)));
    }

    public function testAttributeNotIsValue()
    {
        $matcher = new Matcher(array(
            'id' => array('!$is' => 100)
        ));

        $this->assertFalse($matcher->match(array('id' => 100)));
        $this->assertTrue($matcher->match(array('id' => 300)));
    }

    public function testAttributeNotInList()
    {
        $matcher = new Matcher(array(
            'id' => array('!$in' => array(100, 200))
        ));

        $this->assertFalse($matcher->match(array('id' => 100)));
        $this->assertTrue($matcher->match(array('id' => 300)));
    }

    public function testAttributeNotList()
    {
        $matcher = new Matcher(array(
            'id' => array('$not' => array(100, 200))
        ));

        $this->assertFalse($matcher->match(array('id' => 100)));
        $this->assertTrue($matcher->match(array('id' => 300)));
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidOperator()
    {
        $matcher = new Matcher(array(
            'id' => array('$invalid' => 100)
        ));

        $matcher->match(array('id' => 100));
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidCombinator()
    {
        $matcher = new Matcher(array(
            '$invalid' => array()
        ));

        $matcher->match(array('id' => 100));
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidFilter()
    {
        $matcher = new Matcher('invalid');

        $matcher->match(array('id' => 100));
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidAnd()
    {
        $matcher = new Matcher(array(
            '$and' => 'invalid'
        ));

        $matcher->match(array('id' => 100));
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidOr()
    {
        $matcher = new Matcher(array(
            '$or' => 'invalid'
        ));

        $matcher->match(array('id' => 100));
    }
}
