<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bookmark extends Model
{
    use HasFactory;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url', 'domain'];

    /**
     * Get the bookmark's image URL.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->image) {
                    return asset('assets/images/defaults/bookmark.jpg');
                }

                if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                    return $this->image;
                }

                return asset('storage/'.$this->image);
            },
        );
    }

    /**
     * Get the bookmark's domain.
     */
    protected function domain(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->url) {
                    return null;
                }

                $host = parse_url($this->url, PHP_URL_HOST);

                return $host ? preg_replace('/^www\./', '', $host) : null;
            },
        );
    }

    /*
     * Relationships
     */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
