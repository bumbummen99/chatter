<?php

namespace SkyRaptor\Chatter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class Discussion extends Model
{
    use SoftDeletes;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chatter_discussion';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'chatter_category_id',
        'user_id',
        'slug',
        'color'
    ];

    protected $dates = [
        'deleted_at',
        'last_reply_at'
    ];

    /**
     * The User associated to this Discussion as it's creator.
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(Config::get('chatter.user.namespace'));
    }

    /**
     * THe Category this Discussion is within.
     */
    public function category() : BelongsTo
    {
        return $this->belongsTo(Config::get('chatter.models.category', Category::class), 'chatter_category_id');
    }

    /**
     * The Posts associated to this Discussion as replies.
     */
    public function posts() : HasMany
    {
        return $this->hasMany(Config::get('chatter.models.post', Post::class), 'chatter_discussion_id');
    }

    /**
     * The relation query for the very firt post of this Discussion, basically it's content.
     */
    public function post() : HasMany
    {
        return $this->posts()->orderBy('created_at', 'ASC');
    }

    /**
     * The users associated to this Discussion throught their posts / replies.
     */
    public function users() : BelongsToMany
    {
        return $this->belongsToMany(Config::get('chatter.user.namespace'), Config::get('chatter.models.post', Post::class));
    }

    /**
     * Helper to get the reply / post count. This will produce a query so make
     * sure to Cache it or use the count method on your posts if you already
     * have retrieved them.
     */
    public function getPostsCountAttribute() : int
    {
        return Cache::tags(['chatter-discussions', 'chatter-discussion-' . $this->id])->rememberForever('chatter-discussion-post-count-' . $this->id, function() {
            return $this->posts()->count();
        });
    }

    /**
     * Helper method to toggle to locked state of the 
     * Discussion. Will either toggle with the current state
     * or set to the provided state.
     */
    public function toggleLock(?bool $lock = null) : void
    {
        $this->locked = is_null($lock) ? !$this->locked : $lock;
        $this->save();
    }

    /**
     * Helper method to toggle to hidden state of the 
     * Discussion. Will either toggle with the current state
     * or set to the provided state.
     */
    public function toggleHidden(?bool $hide = null) : void
    {
        $this->hidden = is_null($hide) ? !$this->hidden : $hide;
        $this->save();
    }
}
