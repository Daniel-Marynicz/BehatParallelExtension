<?php

namespace DMarynicz\BehatParallelExtension\Util;

use DMarynicz\BehatParallelExtension\Exception\UnexpectedValue;

class Assert
{
    /**
     * @param mixed $value
     *
     * @return string
     */
    public static function assertString($value)
    {
        if (! is_string($value)) {
            throw new UnexpectedValue('Expected string');
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return array<mixed>
     */
    public static function assertArray($value)
    {
        if (! is_array($value)) {
            throw new UnexpectedValue('Expected array');
        }

        return $value;
    }

    /**
     * @param mixed $value
     *
     * @return int
     */
    public static function assertInt($value)
    {
        if (! is_int($value)) {
            throw new UnexpectedValue('Expected int');
        }

        return $value;
    }
}
