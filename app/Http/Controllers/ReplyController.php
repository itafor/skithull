<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Video;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    /**
     * get replies by comment id
     * @param mixed $commentId
     *
     * @return \Illuminate\Http\Response
     */
    public function getRepliesByComment($commentId)
    {
        $replies = Reply::where([
        ['replyable_id', $commentId],
        ['replyable_type', 'App\BlogModels\BlogComment'],
      ])->orderBy('created_at', 'desc')->with(['replyable','isRepliedBy','likes'])->get();

        if ($replies) {
            return response(['blog_replies' => $replies], 200);
        }
        return response([], 200);
    }

    /**
     * Store a new reply
     * @param Request $request
     *
     ** @return \Illuminate\Http\Response
     */
    public function writeReply(Request $request)
    {
        $validatedData = $request->validate([
            'comment_id' => 'required',
            'body' => 'required',
        ]);
        $reply = Reply::createNew($request->all());
        $comments = $reply->replyable->commentable->comments;// 
        if ($reply) {
        // return response()->json($comments);
           return  Video::fetchComments($comments);
            
        }
        return response(['error' => 'An attempt to write a Reply failed!!'], 500);
    }

    
    /**
     * update a specific reply
     * @param Request $request
     *
     ** @return \Illuminate\Http\Response
     */
    public function editReply(Request $request)
    {
        $validatedData = $request->validate([
            'reply_id' => 'required',
            'body' => 'required',
        ]);

        $reply = Reply::updateReply($request->all());

        if ($reply) {
            return response(['message' => 'Reply updated successfully!','reply' => $reply], 200);
        }
        return response(['error' => 'An attempt to update reply failed!!'], 500);
    }

    /**
     * view a reply
     * @param mixed $comment_id
     * @param mixed $reply_id
     *
     * @return \Illuminate\Http\Response
     */
    public function viewReply($comment_id, $reply_id)
    {
        $reply =  Reply::where([
          ['replyable_type', 'App\Models\Comment'],
          ['replyable_id',$comment_id],
          ['id',$reply_id],
          ])->with(['replyable','isRepliedBy','likes'])->first();

        if ($reply) {
            return response()->json(['reply' => $reply]);
        }
      
        return response()->json([]);
    }

    /**
     * delete a reply
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyReply(Request $request)
    {
        $reply =  Reply::where([
        ['user_id', authUser()->id],
        ['replyable_type', 'App\BlogModels\BlogComment'],
        ['id',$request->id],
        ])->first();

        if ($reply) {
            $reply->delete();
            return response()->json(['message' => 'Reply deleted successfully']);
        }
        
        return response()->json(['error' => 'Something went wrong, please try again']);
    }

    public function displayMoreReplies($commentId, $numberOfReplies){
        if($numberOfReplies){
            
              session()->put('repliesPerPage', $numberOfReplies); 

        return  Video::get_reply_comment($commentId, $numberOfReplies)['output'];
        }
                
    }
}
