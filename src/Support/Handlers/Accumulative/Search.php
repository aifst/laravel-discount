<?php

namespace Aifst\Discount\Support\Handlers\Accumulative;

use Aifst\Discount\Contracts\DiscountHandlerConditions;

/**
 * Class Search
 * @package Aifst\Discount\Handlers\Accumulative
 */
class Search implements DiscountHandlerConditions
{
    /**
     * @return array|mixed
     */
    public function getConditions(): array
    {
        return ['accumulative'];
    }
}
