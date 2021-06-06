<?php

namespace Aifst\Discount;

use Aifst\Discount\Support\DiscountCollection;
use Aifst\Discount\Support\DiscountHandlers;
use Carbon\Carbon;
use Aifst\Discount\Contracts\{Discountable,
    DiscountBuilder as DiscountBuilderContract,
    DiscountClient,
    DiscountModel,
    DiscountOwner
};
use Aifst\Discount\Exceptions\DiscountHandlerNotExists;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Discounts
 * @package   Aifst\Discount
 */
class Discounts
{
    /**
     * @var DiscountBuilderContract
     */
    protected DiscountBuilderContract $builder;

    /**
     * @var DiscountOwner|null
     */
    protected ?DiscountOwner $owner = null;

    /**
     * Discounts constructor.
     * @param DiscountOwner|null $owner
     */
    public function __construct(DiscountBuilderContract $builder, ?DiscountOwner $owner = null)
    {
        $this->builder = $builder;
        $this->owner = $owner;
    }

    /**
     * @param $promo
     * @param DiscountClient|null $client
     * @param Discountable|null $discountable
     * @return DiscountCollection
     */
    public function promo(
        $promo,
        DiscountClient $client = null,
        Discountable $discountable = null
    ): DiscountCollection {
        $discounts = $this->select($client)
            ->wherePromo($promo)
            ->whereNotAutomatic()
            ->whereIsParent()
            ->limit(1)
            ->get()
            ->filter(function ($discount) use ($client, $discountable) {
                try {
                    $result = (new DiscountHandlers)->check($discount, $client, $discountable);
                } catch (DiscountHandlerNotExists $e) {
                    $result = false;
                }
                return $result;
            });

        return new DiscountCollection($discounts);
    }

    /**
     * @param DiscountClient|null $client
     * @param Discountable|null $discountable
     * @return DiscountCollection
     */
    public function auto(DiscountClient $client = null, Discountable $discountable = null): DiscountCollection
    {
        $rules = (new DiscountHandlers)->searchRules($client, $discountable);

        $discounts = $this->select($client)
            ->whereAutomatic()
            ->whereRules($rules)
            ->get()
            ->filter(function ($discount) use ($client, $discountable) {
                try {
                    $result = (new DiscountHandlers)->check($discount, $client, $discountable);
                } catch (DiscountHandlerNotExists $e) {
                    $result = false;
                }
                return $result;
            })
            ->sortByDesc('value');
        /**
         * @TODO realize many discounts logic
         */
        return new DiscountCollection([$discounts->first()]);
    }

    /**
     * @param string $promo
     * @param $value
     * @param int $value_type
     * @param DiscountOwner|null $owner
     * @param string|null $name
     * @param array|null $data
     * @param int|null $start_at
     * @param int|null $deadline
     * @param bool|null $active
     * @return DiscountModel
     */
    public function createPromo(
        string $promo,
        $value,
        $value_type = null,
        ?DiscountOwner $owner = null,
        ?string $name = '',
        ?array $data = null,
        ?int $start_at = null,
        ?int $deadline = null,
        ?bool $active = true
    ): DiscountModel {
        $value_type = $value_type ?? config('discount.default_type');
        $builder = $this->builder
            ->setPromo($promo)
            ->setActive($active)
            ->setValue($value)
            ->setValueType($value_type)
            ->setStartAt($start_at ?? Carbon::now()->timestamp)
            ->setEndAt($deadline ?? $this->getMaxDeadline());

        if ($owner) {
            $builder->setOwner($owner);
        }

        if ($data) {
            $builder->setData($data);
        }

        if ($name) {
            $builder->setName($name);
        }

        $discount = $builder->getDiscount();
        $discount->save();
        return $discount;
    }

    /**
     * @param int $value
     * @param array $data
     * @param int|null $value_type
     * @param DiscountOwner|null $owner
     * @param string|null $name
     * @param int|null $start_at
     * @param int|null $deadline
     * @param bool|null $active
     * @param int|null $parent_id
     * @return DiscountModel
     */
    public function createAuto(
        int $value,
        array $data,
        ?int $value_type = null,
        ?DiscountOwner $owner = null,
        ?string $name = '',
        ?int $start_at = null,
        ?int $deadline = null,
        ?bool $active = true,
        ?int $parent_id = null
    ): DiscountModel {
        $value_type = $value_type ?? config('discount.default_type');
        $builder = $this->builder
            ->setData($data)
            ->setActive($active)
            ->setIsAutomatic(true)
            ->setValue($value)
            ->setValueType($value_type)
            ->setStartAt($start_at ?? Carbon::now()->timestamp)
            ->setEndAt($deadline ?? $this->getMaxDeadline());

        if ($owner) {
            $builder->setOwner($owner);
        }

        if ($name) {
            $builder->setName($name);
        }

        if ($parent_id) {
            $builder->setParent($parent_id);
        }

        $discount = $builder->getDiscount();

        $this->save($discount, (new DiscountHandlers)->dataRules($data));

        return $discount;
    }

    /**
     * @param callable $callback
     * @param DiscountOwner|null $owner
     * @param string|null $name
     * @param int|null $start_at
     * @param int|null $deadline
     * @param bool|null $active
     * @return DiscountModel
     */
    public function createGroup(
        callable $callback,
        ?DiscountOwner $owner = null,
        ?string $name = '',
        ?int $start_at = null,
        ?int $deadline = null,
        ?bool $active = true
    ): DiscountModel {
        $builder = $this->builder
            ->setIsAutomatic(true)
            ->setActive($active)
            ->setStartAt($start_at ?? Carbon::now()->timestamp)
            ->setEndAt($deadline ?? $this->getMaxDeadline());

        if ($owner) {
            $builder->setOwner($owner);
        }

        if ($name) {
            $builder->setName($name);
        }

        $discount = $builder->getDiscount();

        $this->save($discount, []);

        $callback($discount);

        return $discount;
    }

    /**
     * @return string[]
     */
    public function handlerGroups()
    {
        return $this->select(null, 'handlers')
            ->whereAutomatic()
            ->groupBy('handlers')
            ->get()
            ->filter(fn($item) => $item->handlers)
            ->pluck('handlers');
    }

    /**
     * @return Model
     */
    protected function getModel()
    {
        return config('discount.models.discount');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function select(?DiscountClient $client = null, $select = '*')
    {
        $builder = $this->getModel()::query()
            ->select($select);

        if ($this->owner) {
            $builder->whereOwner($this->owner->getModelType(), $this->owner->getModelId());
        }

        if ($client) {
            $builder->whereReusableOrUnused($client);
        }

        $builder->whereActive()
            ->whereNotOverdue();

        return $builder;
    }

    /**
     * @return int
     */
    protected function getMaxDeadline(): int
    {
        return Carbon::now()->timestamp + config('discount.max_deadline');
    }

    /**
     * @param DiscountModel $discount
     * @param $data
     */
    protected function save(DiscountModel $discount, $data)
    {
        DB::transaction(function () use ($discount, $data) {
            $discount->save();
            $this->setRules($discount, $data);
            $this->freshGlobalHandlers();
        });
    }

    /**
     * @param DiscountModel $discount
     * @param array $rules
     */
    protected function setRules(DiscountModel $discount, array $rules)
    {
        $discount_rule = config('discount.models.discount_rule');
        $discount_rule::where('discount_id', $discount->getId())->delete();
        foreach ($rules as $rule) {
            $discount_rule::create(['discount_id' => $discount->getId(), 'hash' => $rule]);
        }
    }

    /**
     *
     */
    protected function freshGlobalHandlers()
    {
        $discount_handler = config('discount.models.discount_handler');
        $discount_handler::truncate();
        $handler_groups = $this->handlerGroups();
        foreach ($handler_groups as $handler_group) {
            $discount_handler::create(['handlers' => $handler_group]);
        }
    }
}
