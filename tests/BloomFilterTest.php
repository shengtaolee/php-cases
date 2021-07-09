<?php
/**
 * BloomFilterTest.php
 * Create by phpstorm
 * Author: santo
 * Date: 2021-07-08
 */

namespace Test;

use PHPUnit\Framework\TestCase;
use Redis;
use Santo\Examples\BloomFilter\BloomFilter;

class BloomFilterTest extends TestCase
{
    public function test(): BloomFilter
    {
        $digests = [];
        $redis = new Redis();
        $redis->connect('127.0.0.1', 6379);
        $filter = new BloomFilter($redis, 'bloom', $digests);
        $this->assertTrue(true);
        return $filter;
    }

    /**
     * @depends test
     */
    public function testAdd1(BloomFilter $filter)
    {
        $this->expectException(\InvalidArgumentException::class);
        $filter->add('');
    }

    /**
     * @depends test
     */
    public function testAdd2(BloomFilter $filter)
    {
        $case = 'test';
        $filter->add($case);
        $this->assertTrue($filter->exists($case));
        $this->assertFalse($filter->exists($case . '2'));

        $case = '测试';
        $filter->add($case);
        $this->assertTrue($filter->exists($case));
        $this->assertFalse($filter->exists($case . '2'));

    }
}