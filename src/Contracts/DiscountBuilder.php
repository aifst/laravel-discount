<?php

namespace Aifst\Discount\Contracts;

/**
 * Interface DiscountBuilder
 * @package Aifst\Discount\Contracts
 */
interface DiscountBuilder
{
    public function reset(): DiscountBuilder;

    public function setOwner(DiscountOwner $owner): DiscountBuilder;

    public function setPromo(string $promo): DiscountBuilder;

    public function setName(string $name): DiscountBuilder;

    public function setParent(int $parent_id): DiscountBuilder;

    public function setStartAt(int $start_at): DiscountBuilder;

    public function setEndAt(int $end_at): DiscountBuilder;

    public function setActive(bool $active): DiscountBuilder;

    public function setIsAutomatic(bool $automatic): DiscountBuilder;

    public function setUsageLimit(int $limit): DiscountBuilder;

    public function setValue(int $value): DiscountBuilder;

    public function setValueType(int $value_type): DiscountBuilder;

    public function setData(array $data): DiscountBuilder;

    public function getDiscount(): DiscountModel;
}
