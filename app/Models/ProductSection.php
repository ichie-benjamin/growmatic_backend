<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ProductSection extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    public function contents() {
        return $this->hasMany(ProductContent::class, 'product_section_id');
    }
}
