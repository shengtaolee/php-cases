<?php

declare(strict_types=1);

namespace Santo\Examples\BloomFilter\Digests;

interface Digest
{
    public function hash($string, $length = null);
}
