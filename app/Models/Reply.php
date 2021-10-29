<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reply extends Model
{
    use HasFactory, SoftDeletes;

    /**
         * The attributes that are mass assignable
         * @var array
         */
    protected $fillable = [
        'user_id',
        'body',
        'replyable_id',
        'replyable_type',
    ];

    /**
     * Get the user that replies to a post comment
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function isRepliedBy()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
    * Get the owning replyable model and the user that commented.
    * @return \Illuminate\Database\Eloquent\Relations\morphTo
    */
    public function replyable()
    {
        return $this->morphTo()->with(['commenter']);
    }

    /**
    * Get all of the reply's likes.
    * @return \Illuminate\Database\Eloquent\Relations\morphMany
    */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable')->with(['isLikedBy']);
    }

    /**
     * Create a new reply
     * @param mixed $data
     *
     * @return \Illuminate\Http\Response
     */
    public static function createNew($data)
    {
        $reply = self::create([
        'user_id' => authUserId(),
        'replyable_id' => $data['comment_id'],
        'replyable_type' => 'App\Models\Comment',
        'body' =>  $data['body'],
        // 'file_url' => isset($data['file_url']) ? uploadImage($data['file_url']) : '',
        ]);

        return $reply;
    }

    /**
     * Update a specific reply
     * @param mixed $data
     * @return \Illuminate\Http\Response
     */
    public static function updateReply($data)
    {
        $reply =  self::where([
        ['user_id', authUserId()],
        ['replyable_type', 'App\Models\Comment',],
        ['id',$data['reply_id']],
        ])->first();
        if ($reply) {
            $reply->body = $data['body'];
            $reply->save();
        }

        return $reply;
    }
}
