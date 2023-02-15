<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Podcast extends Model
{
    use HasFactory, HasUuids;

    public function category() {
        return $this->belongsTo(PodcastCategory::class);
    }
}
