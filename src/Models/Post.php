<?php

namespace SkyRaptor\Chatter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Config;
use GrahamCampbell\Markdown\Facades\Markdown;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use SoftDeletes;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'chatter_post';

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
        'chatter_discussion_id', 
        'user_id', 
        'body', 
        'markdown'
    ];

    protected $dates = [
        'deleted_at'
    ];

    /**
     * Get the Discussion this Post is a part of.
     */
    public function discussion() : BelongsTo
    {
        return $this->belongsTo(Config::get('chatter.models.discussion', Discussion::class), 'chatter_discussion_id');
    }

    /**
     * Get the User who created this Post.
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(Config::get('chatter.user.namespace'));
    }

    /**
     * Helper method to retreive the body. Will return the body as
     * it is or convert from markdown if the flag is set.
     */
    public function getBodyAsHtml() : string
    {
        if ($this->markdown) {
            return Markdown::convertToHtml($this->body);
        } else {
            return $this->body;
        }
    }

    /**
     * Helper method to toggle to locked state of the 
     * Post. Will either toggle with the current state
     * or set to the provided state.
     */
    public function toggleLock(?bool $lock = null) : void
    {
        $this->locked = is_null($lock) ? !$this->locked : $lock;
        $this->save();
    }

    /**
     * Helper method to toggle to hidden state of the 
     * Post. Will either toggle with the current state
     * or set to the provided state.
     */
    public function toggleHidden(?bool $hide = null) : void
    {
        $this->hidden = is_null($hide) ? !$this->hidden : $hide;
        $this->save();
    }
}
