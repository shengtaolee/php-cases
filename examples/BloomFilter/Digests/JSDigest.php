<?php

declare(strict_types=1);

namespace Santo\Examples\BloomFilter\Digests;

/**
 * Invented by Justin Sobel.
 */
class JSDigest implements Digest
{
    public function hash($string, $length = null)
    {
        $hash = 1315423911;

        if (null === $length) {
            $length = strlen($string);
        }

        for ($i = 0; $i < $length; ++$i) {
            $hash ^= (($hash << 5) + ord($string[$i]) + ($hash >> 2));
        }

        return ($hash % 0xFFFFFFFF) & 0xFFFFFFFF;
    }
}
