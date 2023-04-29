<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, HasUuids, Sluggable;



    protected $guarded = [];

    protected $appends = ['thumb_url','editor_url'];

    protected $casts = [
        'published' => 'boolean'
    ];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

//    public function pages()
//    {
//        return $this->morphMany(BuilderPage::class, 'pageable');
//    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'users_projects',
            'project_id',
            'user_id',
        );
    }

    public function domain()
    {
//        return $this->morphOne(CustomDomain::class, 'resource')->select(
//            'id',
//            'host',
////            'resource_id',
////            'resource_type',
//        );
    }

    public function getSettingsAttribute(?string $value): array
    {
        return $value ? json_decode($value, true) : [];
    }


    public function getThumbUrlAttribute(): string
    {
        return env('APP_URL').'/builder/projects/'.$this->user_id.'/'.$this->slug.'/thumbnail.png';
    }

    public function getEditorUrlAttribute(): string
    {
        return route('editor_url', $this->id);
    }

    public function setSettingsAttribute(array $value)
    {
        $this->attributes['settings'] = json_encode($value);
    }

    public function formsEmail(): string
    {
        return $this->settings['formsEmail'] ?? $this->users->first()->email;
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->username,
            'created_at' => $this->created_at->timestamp ?? '_null',
            'updated_at' => $this->updated_at->timestamp ?? '_null',
        ];
    }

    public static function filterableFields(): array
    {
        return ['id', 'created_at', 'updated_at'];
    }
}
