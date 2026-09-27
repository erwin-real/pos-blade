<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'desc'])]
class Category extends Model
{
    public $timestamps = false;

    public function products() { return $this->hasMany(Product::class); }
}
