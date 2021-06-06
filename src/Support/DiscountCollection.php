<?php

namespace Aifst\Discount\Support;

use Illuminate\Support\Collection;

/**
 * Class DiscountCollection
 * @package Aifst\Discount
 */
class DiscountCollection extends Collection
{
    /**
     * @param $price
     * @return float|mixed
     * @throws \Exception
     */
    public function calculatePrice($price)
    {
        $this->sortByDesc('value_type')
            ->groupBy('value_type')
            ->each(function ($group) use (&$price) {
                if (!$first = $group->first()) {
                    return false;
                }
                $sum = $group->sum('value');
                if ($first->value_type == config('discount.types.percent')) {
                    if (($percent = (1 - ($sum / 100))) < 0) {
                        throw new \Exception('Discount cannot be more than 100%');
                    }
                    $price = round($price * $percent, 2);
                } else {
                    if (($price = $price - $sum) < 0) {
                        throw new \Exception('Discount cannot be more than 100%');
                    }
                }
            });

        return $price;
    }
}
