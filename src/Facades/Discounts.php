<?php

namespace Aifst\Discount\Facades;

use Aifst\Discount\Contracts\Discountable;
use Aifst\Discount\Contracts\DiscountClient;
use Aifst\Discount\Contracts\DiscountModel;
use Aifst\Discount\Contracts\DiscountOwner;
use Aifst\Discount\Support\DiscountCollection;
use Illuminate\Support\Facades\Facade;

/**
 * Class Discounts
 * @package Aifst\Discount\Facades
 * @method DiscountCollection promo($promo, DiscountClient $client = null, Discountable $discountable = null)
 * @method DiscountCollection auto(DiscountClient $client = null, Discountable $discountable = null)
 * @method DiscountModel createPromo(string $promo, $value, $value_type = null, ?DiscountOwner $owner = null, ?string $name = '', ?array $data = null, ?int $start_at = null, ?int $deadline = null, ?bool $active = true)
 * @method DiscountModel createAuto(int $value, array $data, ?int $value_type = null, ?DiscountOwner $owner = null, ?string $name = '', ?int $start_at = null, ?int $deadline = null, ?bool $active = true, ?int $parent_id = null)
 * @method DiscountModel createGroup(callable $callback, ?DiscountOwner $owner = null, ?string $name = '', ?int $start_at = null, ?int $deadline = null, ?bool $active = true)
 * @method string[] handlerGroups()
 */
class Discounts extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'discounts';
    }
}
