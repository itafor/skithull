<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoResource;
use App\Models\Comment;
use App\Models\Video;
use Illuminate\Http\Request;
use Validator;

class CommentController extends Controller
{

        /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['loadCommentsWithPagination', 'displayVideoComments']);
    }

     /**
     * Store a new post's comment
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function writeComment(Request $request)
    {

        $validatedData = $request->validate([
            'video_id' => 'required',
            'body' => 'required',
        ]);

        $comment = Comment::createNewComment($request->all());
        
        $data['video'] = $comment->commentable;
        $data['video_comments'] = $data['video']->comments;
        if ($data['video_comments']) {
        
     return  Video::fetchComments($data['video_comments']);
            
        }
        return response(['error' => 'An attempt to video a comment failed!!'], 500);
    }


// Fetch all active video comments
    public function displayVideoComments($video_id){
        $video = Video::findOrFail($video_id);
        if ($video) {
        $data['video_comments'] =  Comment::where([
          ['commentable_type', 'App\Models\Video'],
          ['commentable_id',$video_id],
          ])->with(['commenter','commentable','likes','replies'])->orderBy('created_at', 'desc')->paginate(5); 
        
         return  Video::fetchComments($data['video_comments']);
            
        }
    }

// load more comments  when user scrolled to end of the page

     public function loadCommentsWithPagination($video_id, $number_of_page){
        $video = Video::findOrFail($video_id);
        if ($video) {
        $data['video_comments'] =  Comment::where([
          ['commentable_type', 'App\Models\Video'],
          ['commentable_id',$video_id],
          ])->with(['commenter','commentable','likes','replies'])->orderBy('created_at', 'desc')->paginate($number_of_page); //$video->comments;
        $number_of_comments = count($data['video_comments']);

        if($number_of_page > $number_of_comments){
            return [];
        }
         return Video::fetchComments($data['video_comments']);
       
        }
    }

    /**
    * Update a specific post's comment
    * @param Request $request
    *
    * @return \Illuminate\Http\Response
    */
    public function editComment(Request $request)
    {
        $inputs = $request->all();

         $validator = Validator::make($inputs, [
            'comment_id' => 'required',
            'body' => 'required',
        ]);

         if($validator->fails()){
            return response()->json(['errors'=>'The comment field is required']);
         }

        $comment = Comment::updateComment($inputs);
       
        if ($comment) {
        $video = $comment->commentable;
         $data['video_comments'] = $video->comments;
            return  Video::fetchComments($data['video_comments']);
        }
        return response(['error' => 'An attempt to update comment failed!!'], 500);
    }

    /**
     * View a given comment
     * @param mixed $video_id
     * @param mixed $comment_id
     *
     * @return \Illuminate\Http\Response
     */
    public function viewComment($video_id, $comment_id)
    {
        $comment =  Comment::where([
          ['commentable_type', 'App\Models\Video'],
          ['commentable_id',$video_id],
          ['id',$comment_id],
          ])->with(['commenter','commentable','likes','replies'])->first();

        if ($comment) {
            return response()->json(['comment' => $comment]);
        }
        return response()->json([]);
    }


    /**
     * delete a comment
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyComment($comment_id)
    {
        $comment =  Comment::where([
        ['commenter_id', authUserId()],
        ['commentable_type', 'App\Models\Video'],
        ['id', $comment_id],
        ])->first();

        if ($comment) {
            $comment->delete();
            return response()->json(['message' => 'Comment deleted successfully']);
        }
        return response()->json([]);
    }
    
//// refresh video comments after deleting a comment
        public function refreshVideoCommentsAfterDelete($video_id){
        $video = Video::findOrFail($video_id);
        if ($video) {
        $data['video_comments'] =  Comment::where([
          ['commentable_type', 'App\Models\Video'],
          ['commentable_id',$video_id],
          ])->with(['commenter','commentable','likes','replies'])->orderBy('created_at', 'desc')->paginate(100); 
        
         return  Video::fetchComments($data['video_comments']);
            
        }
    }
}
