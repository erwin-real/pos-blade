<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['name', 'category_id', 'desc', 'stocks', 'price', 'srp', 'expired_at', 'procurement', 'cover_image'])]
class Product extends Model
{
    use SoftDeletes;
    
    protected $table = 'products';

    public $timestamps = true;

    public function category() { return $this->belongsTo(Category::class); }

    public function getisOneMonthLeftOrLess() {
        $date = Carbon::parse($this->expired_at);

        return $date->isPast() || $date->lte(now()->addMonth());
    }
}
