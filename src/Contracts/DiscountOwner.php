<?php

namespace Aifst\Discount\Contracts;

/**
 * Interface DiscountOwner
 * @package Aifst\Discount\Contracts
 */
interface DiscountOwner
{
    public function getModelId();

    public function getModelType();
}
