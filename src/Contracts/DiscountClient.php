<?php

namespace Aifst\Discount\Contracts;

/**
 * Interface DiscountClient
 * @package Aifst\Discount\Contracts
 */
interface DiscountClient
{
    public function getModelId();

    public function getModelType();

    public function getPaidTotal();
}
