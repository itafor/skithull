<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoResource;
use App\Models\Like;
use App\Models\Video;
use Illuminate\Http\Request;

class LikeController extends Controller
{
     

public function __construct(){
    session()->put('repliesPerPage', 2); 
}

    /**
     * Register a new post's like in storage.
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function likeVideo(Request $request)
    {
        $data = $request->all();

        $like =	Like::where([
            'user_id' => authUserId(),
            'likeable_id' => $data['likeable_id'],
            'likeable_type' => 'App\Models\Video',
        ])->first();
        $post = Video::where('id', $data['likeable_id'])->first();
        $isLikedByUserId = Like::where([
        ['user_id', authUserId()],
        ['likeable_id', $data['likeable_id']],
        ['liked', 1],
        [ 'likeable_type', 'App\Models\Video']
        ])->first();
       
        if ($like) {
            $like->liked = $like->liked == 1 ? false : true;
            $like->save();
            return response()->json(['status' => $like->liked, 'data' => new VideoResource($post)]);
        } else {
            $like = Like::create([
             'user_id' => authUserId(),
             'liked' => true,
             'likeable_id' => $data['likeable_id'],
             'likeable_type' => 'App\Models\Video',
            ]);
            return response()->json(['status' => $like->liked]);
        }
    }

    /**
     * Register a new comment's like in storage.
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function likeUnlikeComment($comment_id, $video_id)
    {
        $video = Video::findOrFail($video_id);
    	$comments = $video->comments;
        $like =	Like::where([
     'user_id'=>authUserId(),
     'likeable_id'=> $comment_id,
     'likeable_type'=>'App\Models\Comment',
    ])->first();

        if ($like) {
            $like->liked = $like->liked == 1 ? false : true;
            $like->save();
            return  Video::fetchComments($comments);
        } else {
            $like = Like::create([
     'user_id'=>authUserId(),
     'liked'=>true,
     'likeable_id'=> $comment_id,
     'likeable_type'=>'App\Models\Comment',
        ]);
            return  Video::fetchComments($comments);
        }
    }

    /**
     * Register a new reply's like in storage.
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function likeUnlikeReply($reply_id, $video_id, $commentId)
    {


        $repliesPerPage = session()->get('repliesPerPage');

         $video = Video::findOrFail($video_id);
        $like = Like::where([
     'user_id' => authUserId(),
     'likeable_id' => $reply_id,
     'likeable_type' => 'App\Models\Reply',
    ])->first();

        if ($like) {
            $like->liked = $like->liked == 1 ? false : true;
            $like->save();
            return  Video::get_reply_comment($commentId, $repliesPerPage)['output'];
        } else {
            $like = Like::create([
     'user_id' => authUserId(),
     'liked' => true,
     'likeable_id' => $reply_id,
     'likeable_type' => 'App\Models\Reply',
      ]);
    
            return  Video::get_reply_comment($commentId, $repliesPerPage)['output'];
        }
    }
}
