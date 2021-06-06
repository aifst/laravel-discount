<?php

namespace Aifst\Discount\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DiscountHandler
 * @package Aifst\Discount\Models
 * @property int $id
 * @property string $handlers
 */
class DiscountHandler extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'handlers'
    ];
}
