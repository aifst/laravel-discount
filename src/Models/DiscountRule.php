<?php

namespace Aifst\Discount\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DiscountRule
 * @package Aifst\Discount\Models
 * @property int $id
 * @property int $discount_id
 * @property string $hash
 */
class DiscountRule extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'discount_id',
        'hash'
    ];
}
