<?php

namespace app\enums\base;

/**
 * Base class for enums
 * @package app\enums\data
 * @version 1.0
 *
 * @author Alex Zakharov <alexzakharovwork@gmail.com>
 */
abstract class Enum
{
    /** @var array $list data. */
    protected static $list = [];

    /**
     * List data
     * @return array|null
     */
    public static function listData()
    {
        return static::$list;
    }

    /**
     * @param string $id index of array from listData()
     * @return string|null index of array from listData()
     */
    public static function labelById($id)
    {
        $list = static::listData();
        return isset($list[$id]) ? $list[$id] : null;
    }

    /**
     * @param string $label index of array from listData()
     * @return string|null index of array from listData()
     */
    public static function idByLabel($label)
    {
        $list = array_flip(static::listData());
        return isset($list[$label]) ? $list[$label] : null;
    }
}
