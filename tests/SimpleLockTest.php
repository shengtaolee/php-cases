<?php

declare(strict_types=1);

/**
 * SimpleLockTest.php
 * Create by phpstorm
 * Author: santo
 * Date: 2021-07-09
 */

namespace Test;

use PHPUnit\Framework\TestCase;
use Santo\Examples\RedisLock\SimpleLock;

class SimpleLockTest extends TestCase
{

    public function test(): SimpleLock
    {
        $this->assertTrue(true);
        $lock = new SimpleLock('simple-lock', null, 10);
        return $lock;
    }

    /**
     * @depends test
     */
    public function testLock(SimpleLock $lock)
    {
        $this->assertTrue($lock->lock());
        $this->assertFalse($lock->lock());
        $this->assertTrue($lock->release());
        $this->assertFalse($lock->release());
        $this->assertTrue($lock->lock());
        $this->assertTrue($lock->release());
    }
}