<?php

namespace Aifst\Discount\Support\Handlers\Clients;

use Aifst\Discount\Contracts\DiscountClient;
use Aifst\Discount\Contracts\DiscountHandlerConditions;

/**
 * Class Search
 * @package Aifst\Discount\Handlers\Clients
 */
class Search implements DiscountHandlerConditions
{
    protected ?DiscountClient $client = null;

    /**
     * Data constructor.
     * @param $data
     */
    public function __construct(?DiscountClient $client = null)
    {
        $this->client = $client;
    }

    /**
     * @return array|mixed
     */
    public function getConditions(): array
    {
        return $this->client ? [$this->client->getModelId()] : [];
    }
}
