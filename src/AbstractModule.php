<?php

namespace Console;

abstract class AbstractModule
{
    private static array $instances = [];

    /**
     * @return static
     */
    public static final function getInstance()
    {
        if (!array_key_exists(static::class, self::$instances)) {

            self::$instances[static::class] = new static;
        }

        return self::$instances[static::class];
    }
}