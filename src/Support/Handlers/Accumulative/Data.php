<?php

namespace Aifst\Discount\Support\Handlers\Accumulative;

use Aifst\Discount\Contracts\DiscountHandlerConditions;

/**
 * Class Data
 * @package Aifst\Discount\Handlers\Accumulative
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
        $this->data = isset($data['from']) || isset($data['to']);
    }

    /**
     * @return array
     */
    public function getConditions(): array
    {
        return $this->data ? ['accumulative'] : [];
    }
}
