<?php

return [
    'owner' => \Aifst\Discount\Contracts\DiscountOwner::class,
    'builder' => Aifst\Discount\Support\DiscountBuilder::class,
    'models' => [
        'discount' => Aifst\Discount\Models\Discount::class,
        'discount_rule' => Aifst\Discount\Models\DiscountRule::class,
        'discount_handler' => Aifst\Discount\Models\DiscountHandler::class,
    ],
    'table_names' => [
        'discounts' => 'discounts',
        'discount_statistics' => 'discount_statistics',
        'discount_models' => 'discount_models',
        'discount_rules' => 'discount_rules',
        'discount_handlers' => 'discount_handlers'
    ],
    'column_names' => [
        'model_morph_key' => 'model_id',
    ],
    'handlers' => [
        Aifst\Discount\Support\Handlers\Accumulative::class,
        Aifst\Discount\Support\Handlers\Clients::class,
    ],
    'promo' => [
        'not_found_exception' => false,
        'auto_higher_priority' => true,
    ],
    'default_type' => 1,
    'types' => [
        'percent' => 1,
        'value' => 2
    ],

    'max_deadline' => 100/*years*/ * \Carbon\Carbon::DAYS_PER_YEAR * \Carbon\Carbon::HOURS_PER_DAY *
        \Carbon\Carbon::MINUTES_PER_HOUR * \Carbon\Carbon::SECONDS_PER_MINUTE
];
