<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'category_id', 'desc', 'stocks', 'price', 'srp', 'expired_at', 'procurement', 'cover_image'])]
class Product extends Model
{
    protected $table = 'products';

    public $timestamps = true;

    public function category() { return $this->belongsTo(Category::class); }
}
