<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductContent extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
