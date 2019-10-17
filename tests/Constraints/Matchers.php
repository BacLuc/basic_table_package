<?php


namespace BasicTablePackage\Test\Constraints;


class Matchers
{
    public static function stringContainsAll(array $strings, bool $ignoreCase = false)
    {
        return new StringContainsAll($strings, $ignoreCase);
    }

    public static function stringContainsKeysAndValues(array $keyvalues, bool $ignoreCase = false)
    {
        $keys = array_keys($keyvalues);
        $values = array_values($keyvalues);
        return new StringContainsAll(array_merge($keys, $values), $ignoreCase);
    }
}