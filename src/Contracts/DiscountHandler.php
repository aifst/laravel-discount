<?php

namespace Aifst\Discount\Contracts;

use Aifst\Discount\Contracts\{DiscountClient, Discountable, DiscountModel};

/**
 * Interface Handler
 * @package Aifst\Discount\Handlers
 */
interface DiscountHandler
{
    /**
     * @return string
     */
    public static function name(): string;

    /**
     * @param $data
     * @return DiscountHandler
     */
    public function data($data): DiscountHandler;

    /**
     * @param DiscountClient|null $client
     * @param Discountable|null $discountable
     * @return DiscountHandler
     */
    public function search(
        ?DiscountClient $client = null,
        ?Discountable $discountable = null
    ): DiscountHandler;

    /**
     * @return bool
     */
    public function validate(): bool;

    /**
     * @return array
     */
    public function rules(): array;

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
    ): bool;
}
