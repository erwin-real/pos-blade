<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable('total_amount', 'discount_amount', 'net_amount', 'amount_received', 'total_profit', 'change', 'payment_method', 'payment_status')]
class Order extends Model
{
    use SoftDeletes;

    protected $table = 'orders';

    public $timestamps = true;

    public function order_items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
