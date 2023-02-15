<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id', 'product_type_id', 'title', 'description', 'thumbnail'];

    public function sections() {
        return $this->hasMany(ProductSection::class);
    }

    public function contents() {
        return $this->hasMany(ProductContent::class);
    }

    public function prices() {
        return $this->morphMany(Pricing::class, 'priceable');
    }

    public function detail() {
        return $this->hasOne(ProductDetail::class);
    }

    public function certificate() {
        return $this->hasOne(Certificate::class);
    }
}
