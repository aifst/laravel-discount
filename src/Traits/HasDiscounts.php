<?php

namespace Aifst\Discount\Traits;

use Aifst\Discount\Contracts\DiscountModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Trait HasDiscounts
 * @package   Aifst\Discount\Traits
 */
trait HasDiscounts
{
    /**
     * A model may have multiple discounts.
     */
    public function discounts(): BelongsToMany
    {
        return $this->morphToMany(
            config('discount.models.discount'),
            'model',
            config('discount.table_names.discount_models'),
            config('discount.column_names.model_morph_key'),
            'discount_id'
        );
    }

    /**
     * Assign the given discount to the model.
     *
     * @param ...$discounts
     * @return $this
     */
    public function assignDiscounts(...$discounts): self
    {
        $discounts = collect($discounts)
            ->filter(function ($discount) {
                return $discount instanceof DiscountModel;
            })
            ->map->id
            ->all();

        $model = $this->getModel();

        if ($model->exists) {
            $this->discounts()->sync($discounts, false);
            $model->load('discounts');
        } else {
            $class = \get_class($model);

            $class::saved(
                function ($object) use ($discounts, $model) {
                    $model->discounts()->sync($discounts, false);
                    $model->load('discounts');
                }
            );
        }

        return $this;
    }
}
