<?php

declare(strict_types=1);

/**
 * BloomFilterInterface.php
 * Create by phpstorm
 * Author: santo
 * Date: 2021-07-09
 */

namespace Santo\Examples\BloomFilter;


interface BloomFilterInterface
{
    /**
     * 添加
     *
     * @param string $case
     */
    public function add(string $case): void;

    /**
     * 是否存在
     *
     * @param string $case
     * @return bool
     */
    public function exists(string $case): bool;
}