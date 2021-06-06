<?php

namespace Aifst\Discount\Support;

use Aifst\Discount\Contracts\{Discountable, DiscountClient, DiscountHandler, DiscountModel};
use Aifst\Discount\Exceptions\{DiscountHandlerNotExists, DiscountHandlerRuleNotExists};
use Aifst\Discount\Traits\Hash as HashTrait;

/**
 * Class DiscountHandlers
 * @package Aifst\Discount
 */
class DiscountHandlers
{
    use HashTrait;

    /**
     * @var array
     */
    protected array $handlers;

    /**
     * Handlers constructor.
     */
    public function __construct()
    {
        collect(config('discount.handlers'))->each(function ($item) {
            /**
             * @var DiscountHandler $item
             */
            $this->handlers[$item::name()] = $item;
        });
    }

    /**
     * @param array $hendlers
     * @return \Illuminate\Support\Collection
     */
    public static function getSortHendlers(array $hendlers)
    {
        return collect($hendlers)
            ->sortKeys();
    }

    /**
     * @param array $handlers
     * @throws DiscountHandlerNotExists
     */
    public function checkHandlers(array $handlers)
    {
        foreach ($handlers as $handler) {
            if (!isset($this->handlers[$handler])) {
                throw new DiscountHandlerNotExists('Handler ' . $handler . ' not exists.');
            }
        }
    }

    /**
     * @param $data
     */
    public function dataRules(array $data)
    {
        $rules = collect();
        static::getSortHendlers($data)
            ->each(function ($item, $key) use (&$rules) {
                $handler = $this->getHandlerByName($key);

                if (!($result = $handler->data($item)->rules())) {
                    throw new DiscountHandlerRuleNotExists('Rule for handler ' . $key . ' not exists.');
                }

                $rules = $rules->count() ? $rules->crossJoin($result) : $rules->merge($result);
            });

        $result = [];
        foreach ($rules as $rule) {
            $result[] = $this->hash(is_array($rule) ? implode($rule) : $rule);
        }
        return $result;
    }

    /**
     * @param DiscountClient|null $client
     * @param Discountable|null $discountable
     * @return array
     * @throws DiscountHandlerNotExists
     */
    public function searchRules(?DiscountClient $client = null, ?Discountable $discountable = null)
    {
        $return = [];
        $model = config('discount.models.discount_handler');
        $model::all()
            ->filter(fn($item) => $item->handlers)
            ->pluck('handlers')
            ->each(function ($handlers) use ($client, $discountable, &$return) {
                $rules = collect();
                foreach (explode('.', $handlers) as $name) {
                    $handler = $this->getHandlerByName($name);

                    if (!($result = $handler->search($client, $discountable)->rules())) {
//                        throw new HandlerRuleNotExists('Rule for handler ' . $name . ' not exists.');
                    }

                    $rules = $rules->count() ? $rules->crossJoin($result) : $rules->merge($result);
                }

                foreach ($rules as $rule) {
                    $return[] = $this->hash(is_array($rule) ? implode($rule) : $rule);
                }
            });

        return $return;
    }

    /**
     * @param DiscountModel $discount
     * @param DiscountClient|null $client
     * @param Discountable|null $discountable
     * @return bool
     * @throws DiscountHandlerNotExists
     */
    public function check(
        DiscountModel $discount,
        ?DiscountClient $client = null,
        ?Discountable $discountable = null
    ): bool {
        if ($discount->handlers) {
            foreach (explode('.', $discount->handlers) as $name) {
                $handler = $this->getHandlerByName($name);
                if (!$handler->check($discount, $client, $discountable)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @param $name
     * @return DiscountHandler
     * @throws DiscountHandlerNotExists
     */
    protected function getHandlerByName($name): DiscountHandler
    {
        /**
         * @var DiscountHandler $handler
         */
        if (!(($handler = $this->handlers[$name] ?? null) && $handler = new $handler)) {
            throw new DiscountHandlerNotExists('Handler ' . $name . ' not exists.');
        }

        return $handler;
    }
}
