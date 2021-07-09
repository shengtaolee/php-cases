<?php

declare(strict_types=1);

/**
 * Lock.php
 * Create by phpstorm
 * Author: santo
 * Date: 2021-07-09
 */

namespace Santo\Examples\RedisLock;


interface Lock
{
    public function lock(): bool;

    public function release(): bool;
}