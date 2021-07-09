<?php

declare(strict_types=1);

/**
 * BloomFilter.php
 * Create by phpstorm
 * Author: santo
 * Date: 2021-07-09
 */

namespace Santo\Examples\BloomFilter;

use Santo\Examples\BloomFilter\Digests\BKDRDigest;
use Santo\Examples\BloomFilter\Digests\Digest;
use Santo\Examples\BloomFilter\Digests\DJBDigest;
use Santo\Examples\BloomFilter\Digests\SDBMDigest;

class BloomFilter implements BloomFilterInterface
{
    /**
     * PHPRedis对象
     * @var \Redis
     */
    protected $redis;

    /**
     * 默认hash
     * @var string[]
     */
    protected $digests = [
        BKDRDigest::class,
        SDBMDigest::class,
        DJBDigest::class,
    ];

    /**
     * 布隆过滤器使用的bitmap key
     * @var string
     */
    protected $bitMapKey;

    /**
     * 布隆过滤器使用的bitmap数量
     * @var int
     */
    protected $partitionsCount;

    /**
     * BloomFilter constructor.
     * @param \Redis $redis
     * @param null $bitMapKey Bitmap key, 当分区数量大于1, 自动产生key, 设置的key无效
     * @param null $digests Hash方法数组
     * @param int $partitionsCount 分区数量, 即当前布隆过滤器使用多少个bitmap
     */
    public function __construct(\Redis $redis, $bitMapKey = null, $digests = null, $partitionsCount = 1)
    {
        $this->redis = $redis;

        if (!empty($bitMapKey)) {
            $this->bitMapKey = $bitMapKey;
        }

        if (!empty($digests)) {
            $this->digests = $digests;
        }

        $this->partitionsCount = $partitionsCount;
    }

    public function add(string $case): void
    {
        $this->checkCaseValue($case);

        $key = $this->getBitMapKey($case);

        $pipe = $this->redis->multi();
        foreach ($this->digests as $digest) {
            /** @var Digest $object */
            $object = new $digest;
            $pipe->setBit($key, $object->hash($case), true);
        }
        $pipe->exec();
    }

    public function exists(string $case): bool
    {
        $this->checkCaseValue($case);

        $key = $this->getBitMapKey($case);

        $pipe = $this->redis->multi();
        foreach ($this->digests as $digest) {
            /** @var Digest $object */
            $object = new $digest;
            $pipe->getBit($key, $object->hash($case));
        }
        $result = $pipe->exec();

        foreach ($result as $bit) {
            if (0 == $bit) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $case
     * @return string
     */
    protected function getBitMapKey($case): string
    {
        if ($this->partitionsCount < 1) {
            throw new \InvalidArgumentException('The number of partitions must be greater than or equal to 1.');
        }

        if ($this->partitionsCount == 1) {
            if (!$this->bitMapKey) {
                throw new \InvalidArgumentException('Need to set bitmap key.');
            }
            return $this->bitMapKey;
        }

        return 'BloomFilter:' . (crc32($case) % $this->partitionsCount);
    }

    /**
     * @param string $case
     */
    protected function checkCaseValue(string $case): void
    {
        if (empty($case)) {
            throw new \InvalidArgumentException('The case can not be empty or null');
        }
    }
}