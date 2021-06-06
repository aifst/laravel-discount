<?php

namespace Aifst\Discount\Traits;

/**
 * Trait Hash
 * @package Aifst\Discount\Traits
 */
trait Hash
{
    /**
     * @param $data
     * @return string
     */
    protected function hash($data): string
    {
        return md5(is_array($data) ? json_encode($data) : $data);
    }
}
