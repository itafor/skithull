<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'commenter_id',
        'body',
        'commentable_id',
        'commentable_type',
    ];

    /**
    * Get the owning commentable model.
    * @return \Illuminate\Database\Eloquent\Relations\morphTo
    */
    public function commentable()
    {
        return $this->morphTo()->with(['user']);
    }

    /**
    * Get the user that commented.
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
    */
    public function commenter()
    {
        return $this->belongsTo(User::class, 'commenter_id', 'id');
    }

    /**
    * Get all of the commemt's likes.
    * @return \Illuminate\Database\Eloquent\Relations\morphMany
    */
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable')->with(['isLikedBy']);
    }

    /**
     * Get all of the comment's replies
     * @return \Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function replies()
    {
        return $this->morphMany(Reply::class, 'replyable')->with(['isRepliedBy','likes'])->orderBy('created_at', 'desc');
    }

    /**
     * store a new blog comment
     * @param mixed $data
     *
     * @return \Illuminate\Http\Response
     */
    public static function createNewComment($data)
    {
        $comment = self::create([
        'commenter_id' => authUserId(),
        'commentable_id' => $data['video_id'],
        'commentable_type' => 'App\Models\Video',
        'body' =>  $data['body'],
        ]);

        return $comment;
    }

    /**
     * Update the specified resource in storage.
     * @param mixed $data
     *
     * @return \Illuminate\Http\Response
     */
    public static function updateComment($data)
    {
        $comment =  self::where([
        ['commenter_id', authUserId()],
        ['commentable_type', 'App\Models\Video'],
        ['id', $data['comment_id']],
        
    ])->first();

        if ($comment) {
            $comment->body = $data['body'];
            $comment->save();
        }

        return $comment;
    }
}
