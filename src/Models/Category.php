<?php

namespace SkyRaptor\Chatter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chatter_categories';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = [
        'children'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'color',
        'order',
        'parent_id',
    ];

    public function discussions() : HasMany
    {
        return $this->hasMany(Models::className(Discussion::class),'chatter_category_id');
    }

    public function parent() : BelongsTo
    {
        return $this->belongsTo(Models::classname(self::class), 'parent_id');
    }

    public function children() : HasMany
    {
        return $this->hasMany(Models::classname(self::class), 'parent_id')->orderBy('order', 'asc');
    }
    
    public function isChildOf(int $parentId) : bool
    {
        $category = $this;

        while (($parent = $category->parent)) {
            if ($parent->id === $parentId) {
                return true;
            } else {
                $category = $parent;
            }
        }

        return false;
    }
}
