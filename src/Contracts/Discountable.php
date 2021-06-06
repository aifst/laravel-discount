<?php

namespace Aifst\Discount\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Interface Discountable
 * @package Aifst\Discount\Contracts
 */
interface Discountable
{
    public function getModelId();

    public function getModelType();

    public function discounts(): BelongsToMany;
}
