<?php

declare(strict_types=1);

/**
 * SimpleLock.php
 * Create by phpstorm
 * Author: santo
 * Date: 2021-07-09
 */

namespace Santo\Examples\RedisLock;

use InvalidArgumentException;
use Redis;

class SimpleLock implements Lock
{
    /**
     * 锁名
     * @var string
     */
    protected $key;

    /**
     * 锁的值, 释放锁时根据该值判断, 是否持有锁
     * @var string
     */
    protected $value;

    /**
     * 锁有效时间, 秒
     * @var int
     */
    protected $expire;

    /**
     * @var Redis
     */
    protected $redis;

    public function __construct(string $key, string $value = null, int $expire = 3)
    {
        if (empty($key)) {
            throw new InvalidArgumentException('the key can not be null or empty.');
        }

        $this->key = $key;

        $this->value = !empty($value) ? $value : uniqid((string)mt_rand(), true);

        $this->expire = $expire;

        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    public function lock(int $tries = 3): bool
    {
        while ($tries > 0) {
            $result = $this->redis->set($this->key, $this->value, ['NX', 'EX' => $this->expire]);
            if ($result) {
                return true;
            }
            $tries--;
            // 休眠10毫秒再重试
            usleep(10 * 1000);
        }

        return false;
    }

    public function release(): bool
    {
        $script = <<< EOF
if redis.call("get", KEYS[1]) == ARGV[1] then
    return redis.call("del", KEYS[1])
else
    return 0
end
EOF;
        $result = $this->redis->eval($script, [$this->key, $this->value], 1);

        return $result > 0;
    }
}