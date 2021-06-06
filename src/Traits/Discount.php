<?php

namespace Aifst\Discount\Traits;

use Aifst\Discount\Contracts\DiscountClient;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Trait HasDiscounts
 * @package   Aifst\Discount\Traits
 * @method Builder|$this wherePromo(string $promo)
 * @method Builder|$this whereOwner(string $owner_model_type, int $owner_model_id)
 * @method Builder|$this whereReusableOrUnused(DiscountClient $client)
 * @method Builder|$this whereActive()
 * @method Builder|$this whereNotOverdue()
 * @method Builder|$this whereIsParent()
 * @method Builder|$this whereAutomatic()
 * @method Builder|$this whereNotAutomatic()
 */
trait Discount
{
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Builder $builder
     * @param string $promo
     */
    public function scopeWherePromo(Builder $builder, string $promo)
    {
        $builder->where('code', $promo);
    }

    /**
     * @param Builder $builder
     * @param string $owner_model_type
     * @param int $owner_model_id
     */
    public function scopeWhereOwner(Builder $builder, string $owner_model_type, int $owner_model_id)
    {
        $builder->where('owner_model_type', $owner_model_type)
            ->where('owner_model_id', $owner_model_id);
    }

    /**
     * @param Builder $builder
     */
    public function scopeWhereAutomatic(Builder $builder)
    {
        $builder->where('is_automatic', true);
    }

    /**
     * @param Builder $builder
     */
    public function scopeWhereNotAutomatic(Builder $builder)
    {
        $builder->where('is_automatic', false);
    }

    /**
     * @param Builder $builder
     */
    public function scopeWhereActive(Builder $builder)
    {
        $builder->where('active', true);
    }

    /**
     * @param Builder $builder
     */
    public function scopeWhereIsParent(Builder $builder)
    {
        $builder->whereNull('parent_id');
    }

    /**
     * @param Builder $builder
     */
    public function scopeWhereNotOverdue(Builder $builder)
    {
        $timestamp = Carbon::now()->timestamp;
        $builder->where('start_at', '<=', $timestamp)
            ->where('end_at', '>=', $timestamp);
    }

    /**
     * @param Builder $builder
     * @param DiscountClient $client
     */
    public function scopeWhereReusableOrUnused(Builder $builder, DiscountClient $client)
    {
        $builder->select(config('discount.table_names.discounts') . '.*')
            ->leftJoin(config('discount.table_names.discount_statistics'),
                config('discount.table_names.discount_statistics') . '.discount_id',
                DB::Raw(config('discount.table_names.discounts') . '.id')
            )
            ->where(function ($query) use ($client) {
                $query->whereNull('usage_limit')
                    ->orWhere('usage_limit', '>',
                        DB::Raw(config('discount.table_names.discount_statistics') . '.count'));
            });
    }

    /**
     * @param Builder $builder
     * @param array $rules
     */
    public function scopeWhereRules(Builder $builder, array $rules)
    {
        $builder->whereExists(function ($query) use ($rules) {
            $query->select(DB::raw(1))
                ->from(config('discount.table_names.discount_rules'))
                ->whereColumn(config('discount.table_names.discount_rules') . '.discount_id',
                    config('discount.table_names.discounts') . '.id')
                ->whereIn('hash', $rules);
        });
    }
}
