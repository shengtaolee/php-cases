<?php

declare(strict_types=1);

/**
 * SingletonTrait.php
 * Create by phpstorm
 * Author: santo
 * Date: 2021-07-09
 */

namespace Santo\Examples\Traits;


trait SingletonTrait
{
    private static $instance;

    public static function getInstance()
    {
        if (! (self::$instance instanceof self)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
    }
}