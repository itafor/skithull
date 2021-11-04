<?php

namespace App\Models;

use App\Models\Reply;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Video extends Model implements Viewable
{
    use HasFactory, SoftDeletes, InteractsWithViews;

       /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'video_title',
        'video_url',
        'user_id',
        'uuid',
        'channel',
        'author',
        'description',
        'status',
    ];

public function user()
{
    return $this->belongsTo(User::class,'user_id','id');
}

/**
   * Get all of the post's comments.
   * @return \Illuminate\Database\Eloquent\Relations\MorphMany
   */
public function comments()
{
    return $this->morphMany(Comment::class, 'commentable')->with(['commenter','likes','replies','commentable'])->orderBy('created_at', 'desc');
}

/**
    * Get all of the post's likes.
    * @return \Illuminate\Database\Eloquent\Relations\MorphMny
    */
public function likes()
{
    return $this->morphMany(Like::class, 'likeable')->with(['isLikedBy']);
}

public static function uploadNewVideo($data) {

  $embededVideoYouTubeVideo = self::embedYoutubeVideo($data);

    $video = self::create([
    'user_id' => auth()->user()->id,
    'video_title' => $data['video_title'],
    'channel' => $data['channel'],
    'author' => $data['author'],
    'description' => isset($data['description']) ? $data['description'] : null,
    'uuid' => generateUUID(),
    'title_slug' => Str::slug($data['video_title'], '-'),
    'video_url' => isset($data['video_url']) && $data['channel'] == 'youTube'  ? $embededVideoYouTubeVideo : uploadVideo($data['video_url']),

    ]);

    return $video;
}

public static function embedYoutubeVideo($data){
    if($data['channel'] == 'youTube' && stripos($data['video_url'], "watch?v=") !== false)
        {

     $embededVideo =  str_replace("watch?v=", "embed/", $data['video_url']);
              return $embededVideo;
               
      }else{
              return $data['video_url'];
      }
    }

public static function fetchComments($comments){

$numberOfreplies = 1;
  $output = '';
foreach($comments as $comment)
{
 $output .= '
<div class="row">
<div class="col-md-12">
<div class="row">
<div class="col-md-12">
  <div class="media"> 
     
      <p data-letters="'.getFirstLetterOfUserNames($comment->commenter->first_name.' '.$comment->commenter->last_name).'"> </p>
      <div class="media-body">
          <div class="row">
              <div class="col-8 d-flex">
                  <h5><b>'.$comment->commenter->first_name.' '.$comment->commenter->last_name.'</b></h5> <span>- '.Carbon::parse($comment->created_at)->diffForHumans().'</span>
              </div>
              <div class="col-4">
                  <div class="pull-right reply"><span onclick="replyVideoComment('.$comment->id.')"><i class="fa fa-reply"></i> reply</span> </div>
              </div>
          </div> '.$comment->body.' 
<div class="col-8 mb-5">
<div class="pull-left">
<span class="mr-2 " onclick="likeOrDislikeComment('.$comment->id.', '.$comment->commentable->id.')"> <i class="fa fa-thumbs-up  '.commentLikedBy($comment->id).'" style="cursor:pointer;"></i> '.commentLikesCount($comment->id).' likes
</span>';
if(auth()->user() && $comment->commenter->id == auth()->user()->id){
  $output .= '<span class="mr-2" style="cursor:pointer;" onclick="toggleEditable_comment_formdiv('.$comment->id.')">Edit</span>';
}

if(auth()->user() && $comment->commenter->id == auth()->user()->id){
$output .= '<span class="mr-2 text-danger" onclick="deleteVideoComment('.$comment->id.')" style="cursor:pointer;">Delete </span>';
}

$output .='<span class="mr-2" title="Report"><i class="fa fa-flag"></i></span>';
$output .= '<span id="totalNumberOfReplies'.$comment->id.'" style="display:none;">'.count($comment->replies).'</span>';

$output .= '</div>

<div class="pull-right ml-50">
<span onclick="toggleReplies('.$comment->id.')" style="cursor:pointer; color:gray;" >View replies('.count($comment->replies).')  </span>

</div>

</div>';
// Edit comment form
$output .='<div class="row mt-4" id="editable_comment_formdiv'.$comment->id.'form" style="display: none;">
<form method="POST" autocomplete="off" class="mt--3" id="video_comment_reply_form">
<div class="row">
<input type="hidden" name="video_id" id="video_id'.$comment->id.'" value="'.$comment->commentable->id.'">
<input type="hidden" name="comment_id" id="editable_comment_id'.$comment->id.'" value="'.$comment->id.'">

</div>
<div class="row">
<div class="col-md-12" style="width: 600px;">
  <div>

      <textarea class="form-control" name="body" id="editable_comment_body'.$comment->id.'"  placeholder="Type a comment" rows="2" required>'.$comment->body.'</textarea>
  </div>
</div>
</div>
<div class="text-center mt-2">
<button type="button" onclick="toggleEditable_comment_formdiv('.$comment->id.')" class="btn btn-warning mr-2">Cancel</button>

<button type="button" onclick="saveUpdatedComment('.$comment->id.')" class="btn btn-success" >Update comment</button>

</div>
</form>
</div>';
// Reply to comment form
$output .='<div class="row mt-4" id="replyToVideoComment'.$comment->id.'form" style="display: none;">
<form method="POST" autocomplete="off" class="mt--3" id="video_comment_reply_form">
<div class="row">
<input type="hidden" name="video_id" id="video_id'.$comment->id.'" value="'.$comment->commentable->id.'">
<input type="hidden" name="comment_id" id="comment_id'.$comment->id.'" value="'.$comment->id.'">

</div>
<div class="row">
<div class="col-md-12" style="width: 600px;">
  <div>

      <textarea class="form-control" name="body" id="reply_body'.$comment->id.'"  placeholder="Type a reply" rows="2" required></textarea>
  </div>
</div>
</div>
<div class="text-center mt-2">
<button type="button" onclick="replyVideoComment('.$comment->id.')" class="btn btn-warning mr-2">Cancel</button>

<button type="button" onclick="submitVideoCommentReplyForm('.$comment->id.')" class="btn btn-success" >Save reply</button>

</div>
</form>
</div>';
// Reply to comment form end
$output .= '<div id="repliesContainer'.$comment->id.'" style="display: none;">';
$output .= self::get_reply_comment($comment->id)['output'];
$output .='</div>';
$output .= '<span onclick="showMoreReplies('.$comment->id.')" id="showMoreRepliesHolder'.$comment->id.'" style="cursor: pointer; display: none;">';
$output .= (int) self::get_reply_comment($comment->id)['currentRepliesCount'] == count($comment->replies) ? '' : 'see '.count($comment->replies) - (int) self::get_reply_comment($comment->id)['currentRepliesCount'].' more replies';
$output .='</span>';
          
$output.='             
      </div>
  </div>
 
</div>
</div>
</div>
</div>

 ';
 // $output .= get_reply_comment($connect, $row["comment_id"]);
}

return [
    'comments' => $output,
    'noumberOfComments' => count($comments)
];
    }

public static function get_reply_comment($comment_id, $pages = 2)
{

   $replies =  Reply::where([
          ['replyable_type', 'App\Models\Comment'],
          ['replyable_id',$comment_id],
          ])->with(['replyable','isRepliedBy','likes'])->orderBy("created_at", "desc")->paginate($pages);
$output = '';
 if(count($replies) > 0)
 {

  foreach($replies as $reply)
  {
   $output .= '
<div class="media mt-4 mb-4">
  <p data-letters="'.getFirstLetterOfUserNames($reply->isRepliedBy->first_name.' '.$reply->isRepliedBy->last_name).'"> </p>
  <div class="media-body">
      <div class="row">
          <div class="col-12 d-flex">
              <h5>'.$reply->isRepliedBy->first_name.' '.$reply->isRepliedBy->last_name.'</h5> <span>- '.Carbon::parse($reply->created_at)->diffForHumans().'</span>
          </div>
      </div>'.$reply->body.'
        <div class="row">
<span class="mr-2" onclick="likeOrUnlikeReply('.$reply->id.','.$reply->replyable->commentable->id.','.$reply->replyable->id.')"> <i class="fa fa-thumbs-up mr-1  '.replyLikedBy($reply->id).'"></i>'.replyLikeCounts($reply->id).' likes
</span>';
if(auth()->user() && $reply->isRepliedBy->id == auth()->user()->id){
$output .='<span class="mr-2" onclick="toggleEditable_reply_formdiv('.$reply->id.')">Edit</span>';
}
if(auth()->user() && $reply->isRepliedBy->id == auth()->user()->id){
$output .='<span class="mr-2 text-danger" onclick="deleteReply('.$reply->id.','.$reply->replyable->id.')">Delete</span>';
}
$output .='<span class="mr-2" title="Report"><i class="fa fa-flag"></i></span>
<span class="mr-2 pull-right"   style="cursor:pointer;" title="Reply"><i class="fa fa-reply mr-1"></i>Reply</span>
</div>
  <div class="row" id="editable_reply_formdiv'.$reply->id.'form" style="display: none;">

<form method="POST" autocomplete="off" class="mt--3" id="video_reply_form">
<div class="row">
<input type="hidden" name="reply_id" id="editablereply_id'.$reply->id.'" value="'.$reply->id.'">

</div>
<div class="row">
<div class="col-md-12" style="width: 600px;">
  <div>

      <textarea class="form-control" name="body" id="editablereply_body'.$reply->id.'"  placeholder="Type a reply" rows="2" required>'.$reply->body.'</textarea>
  </div>
</div>
</div>
<div class="text-center mt-2">
<button type="button" onclick="toggleEditable_reply_formdiv('.$reply->id.')" class="btn btn-warning mr-2">Cancel</button>

<button type="button" onclick="updateCommentReplyForm('.$reply->id.','.$reply->replyable->id.')" class="btn btn-success" >Update reply</button>

</div>
</form>


  </div>
  </div>
</div>
   ';
   // $output .= self::get_reply_comment($reply->replyable->id);
  }
  $output .= '<span id="numberOfVisibleReplies'.$comment_id.'" style="display: none;">'.count($replies).'</span>';
 }
 return [
  'output' => $output,
   'currentRepliesCount' => count($replies),
 ]; 
}


}
