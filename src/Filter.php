<?php

namespace Clue\JsonQuery;

interface Filter
{
    /**
     * Checks whether this filter instance matches the given $object
     *
     * @param array|object $object
     * @return boolean
     */
    public function doesMatch($object);
}
