<?php

namespace Aifst\Discount\Support\Handlers\Clients;

use Aifst\Discount\Contracts\DiscountHandlerConditions;

/**
 * Class Data
 * @package Aifst\Discount\Handlers\Clients
 */
class Data implements DiscountHandlerConditions
{
    protected $data;

    /**
     * Data constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = is_array($data) ?
            collect($data)->filter(fn($item) => is_numeric($item))->toArray() : [];
    }

    /**
     * @return array
     */
    public function getConditions(): array
    {
        return $this->data;
    }
}