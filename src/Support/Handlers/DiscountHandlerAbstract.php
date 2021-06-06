<?php

namespace Aifst\Discount\Support\Handlers;

use Aifst\Discount\Contracts\{Discountable, DiscountClient, DiscountHandler, DiscountHandlerConditions};
use Aifst\Discount\Traits\Hash as HashTrait;

/**
 * Class DiscountHandlerAbstract
 * @package Aifst\Discount\Handlers
 */
abstract class DiscountHandlerAbstract implements DiscountHandler
{
    use HashTrait;

    /**
     * @var
     */
    protected $data;

    /**
     * @var DiscountHandlerConditions
     */
    protected DiscountHandlerConditions $dataConstructor;

    /**
     * @return string
     */
    abstract public static function name(): string;

    /**
     * @param $data
     * @return DiscountHandlerConditions
     */
    abstract protected function dataCondition($data): DiscountHandlerConditions;

    /**
     * @param DiscountClient|null $client
     * @param Discountable|null $discountable
     * @return DiscountHandlerConditions
     */
    abstract protected function searchCondition(
        ?DiscountClient $client = null,
        ?Discountable $discountable = null
    ): DiscountHandlerConditions;

    /**
     * @param $data
     * @return DiscountHandler
     */
    public function data($data): DiscountHandler
    {
        $this->dataConstructor = $this->dataCondition($data);
        return $this;
    }

    /**
     * @param DiscountClient|null $client
     * @param Discountable|null $discountable
     * @return DiscountHandler
     */
    public function search(
        ?DiscountClient $client = null,
        ?Discountable $discountable = null
    ): DiscountHandler {
        $this->dataConstructor = $this->searchCondition($client);
        return $this;
    }

    /**
     * @param $data
     * @return bool
     */
    public function validate(): bool
    {
        if (!$this->dataConstructor || !$this->dataConstructor->getConditions()) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $result = [];
        collect($this->dataConstructor->getConditions())
            ->each(function ($item) use (&$result) {
                $result[] = $this->hash($item);
            });

        return $result;
    }
}
