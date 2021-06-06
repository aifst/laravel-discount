<?php

namespace Aifst\Discount\Support;

use Aifst\Discount\Contracts\{DiscountModel, DiscountOwner};
use Aifst\Discount\Contracts\DiscountBuilder as DiscountBuilderContract;

/**
 * Class DiscountBuilder
 * @package Aifst\Discount
 */
class DiscountBuilder implements DiscountBuilderContract
{
    /**
     * @var ?DiscountModel
     */
    private ?DiscountModel $discount = null;

    /**
     * OrderBuilder constructor.
     */
    public function __construct(?DiscountModel $discount = null)
    {
        $this->reset($discount);
    }

    /**
     * @param DiscountModel|null $discount
     * @return DiscountBuilderContract
     */
    public function reset(?DiscountModel $discount = null): DiscountBuilderContract
    {
        if ($discount) {
            $this->discount = $discount;
        } else {
            $model = config('discount.models.discount');
            $this->discount = new $model;
        }

        return $this;
    }

    /**
     * @param DiscountOwner $owner
     * @return DiscountBuilderContract
     */
    public function setOwner(DiscountOwner $owner): DiscountBuilderContract
    {
        $this->discount->owner_model_type = $owner->getModelType();
        $this->discount->owner_model_id = $owner->getModelId();
        return $this;
    }

    /**
     * @param string $promo
     * @return DiscountBuilderContract
     */
    public function setPromo(string $promo): DiscountBuilderContract
    {
        $this->discount->code = $promo;
        $this->discount->is_automatic = false;
        return $this;
    }

    /**
     * @param string $name
     * @return DiscountBuilderContract
     */
    public function setName(string $name): DiscountBuilderContract
    {
        $this->discount->name = $name;
        return $this;
    }

    /**
     * @param int $parent_id
     * @return DiscountBuilderContract
     */
    public function setParent(int $parent_id): DiscountBuilderContract
    {
        $this->discount->parent_id = $parent_id;
        return $this;
    }

    /**
     * @param int $start_at
     * @return DiscountBuilderContract
     */
    public function setStartAt(int $start_at): DiscountBuilderContract
    {
        $this->discount->start_at = $start_at;
        return $this;
    }

    /**
     * @param int $end_at
     * @return DiscountBuilderContract
     */
    public function setEndAt(int $end_at): DiscountBuilderContract
    {
        $this->discount->end_at = $end_at;
        return $this;
    }

    /**
     * @param bool $active
     * @return DiscountBuilderContract
     */
    public function setActive(bool $active): DiscountBuilderContract
    {
        $this->discount->active = $active;
        return $this;
    }

    /**
     * @param bool $automatic
     * @return DiscountBuilderContract
     */
    public function setIsAutomatic(bool $automatic): DiscountBuilderContract
    {
        if (!$this->discount->code) {
            $this->discount->is_automatic = $automatic;
        }
        return $this;
    }

    /**
     * @param int $limit
     * @return DiscountBuilderContract
     */
    public function setUsageLimit(int $limit): DiscountBuilderContract
    {
        $this->discount->usage_limit = $limit;
        return $this;
    }

    /**
     * @param int $value
     * @return DiscountBuilderContract
     */
    public function setValue(int $value): DiscountBuilderContract
    {
        $this->discount->value = $value;
        return $this;
    }

    /**
     * @param int $value_type
     * @return DiscountBuilderContract
     */
    public function setValueType(int $value_type): DiscountBuilderContract
    {
        $this->discount->value_type = $value_type;
        return $this;
    }

    /**
     * @param array $data
     * @return DiscountBuilderContract
     */
    public function setData(array $data): DiscountBuilderContract
    {
        $handlers = array_keys($data);
        (new DiscountHandlers)->checkHandlers($handlers);

        $this->discount->data = $data;

        $this->discount->handlers = (new DiscountHandlers)->getSortHendlers($handlers)
            ->implode('.');

        return $this;
    }

    /**
     * @return DiscountModel
     */
    public function getDiscount(): DiscountModel
    {
        $result = $this->discount;
        $this->reset();

        return $result;
    }
}
