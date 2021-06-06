<?php

namespace Aifst\Discount\Support\Handlers;

use Aifst\Discount\Contracts\{DiscountClient, Discountable, DiscountModel, DiscountHandler, DiscountHandlerConditions};
use Aifst\Discount\Support\Handlers\Clients\{Data, Search};
use Illuminate\Support\Arr;

/**
 * Class Customers
 * format: data['clients'] = [1,2,3]
 * @package   Aifst\Discount\Handlers
 */
class Clients extends DiscountHandlerAbstract implements DiscountHandler
{
    /**
     * @return string
     */
    public static function name(): string
    {
        return 'clients';
    }

    /**
     * @param $data
     * @return DiscountHandlerConditions
     */
    protected function dataCondition($data): DiscountHandlerConditions
    {
        return new Data($data);
    }

    /**
     * @param DiscountClient|null $client
     * @param Discountable|null $discountable
     * @return DiscountHandlerConditions
     */
    protected function searchCondition(
        ?DiscountClient $client = null,
        ?Discountable $discountable = null
    ): DiscountHandlerConditions {
        return new Search($client);
    }

    /**
     * @param DiscountModel $discount
     * @param DiscountClient|null $client
     * @param Discountable|null $discountable
     * @return bool
     */
    public function check(
        DiscountModel $discount,
        ?DiscountClient $client = null,
        ?Discountable $discountable = null
    ): bool {
        if ($client && is_array($data = Arr::get($discount->data, $this->name())) &&
            in_array($client->getModelId(), $data)) {
            return true;
        }

        return false;
    }
}
