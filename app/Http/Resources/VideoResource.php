<?php

namespace App\Http\Resources;

use App\Models\Comment;
use App\Models\Like;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
          return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'video_title' => $this->video_title,
            'video_url' => $this->video_url,
            'uuid' => $this->uuid,
            'created_at' => Carbon::parse($this->created_at)->toFormattedDateString(),
            'author' => $this->author,
            'user' => $this->user,
            'comments' => $this->comments,
            'likes' => $this->likes,
            'description' => $this->description,
            'status' => $this->status,
            'channel' => $this->channel,
            'likes_count' => count(Like::where([
                        ['liked',1],
                        ['likeable_id',$this->id],
                        ['likeable_type','App\Models\Video']
                        ])->get()),
             'comments_count' => count(Comment::where([
                        ['commentable_id', $this->id],
                        ['commentable_type','App\Models\Video']
                        ])->get()),
        ];
    }
}
