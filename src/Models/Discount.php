<?php

namespace Aifst\Discount\Models;

use Aifst\Discount\Contracts\DiscountModel;
use Aifst\Discount\Traits\Discount as DiscountTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Disount
 * @package Aifst\Discount\Models
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $parent_id
 * @property string $owner_model_type
 * @property int $owner_model_id
 * @property boolean $active
 * @property bool $is_automatic
 * @property int $usage_limit
 * @property int $value
 * @property int $value_type
 * @property array $data
 * @property string $handlers
 */
class Discount extends Model implements DiscountModel
{
    use DiscountTrait;

    protected $fillable = [
        'code',
        'name',
        'parent_id',
        'owner_model_type',
        'owner_model_id',
        'active',
        'is_automatic',
        'usage_limit',
        'value',
        'value_type',
        'data',
        'handlers',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
