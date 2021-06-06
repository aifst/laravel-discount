<?php

namespace Aifst\Discount\Contracts;

/**
 * Interface DiscountHandlerConditions
 * @package Aifst\Discount\Handlers
 */
interface DiscountHandlerConditions
{
    public function getConditions(): array;
}
