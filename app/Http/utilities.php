<?php

use App\Models\Like;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Str;


// $GLOBALS['repliesPerPage'] = 2;
function authUserId()
{
   return auth()->user() ? auth()->user()->id : '';
}

function generateUUID()
{
    return Str::uuid()->toString();
}

function uploadImage($image)
{
    if (isset($image)) {
        if ($image->isValid()) {
            $trans = array(
                ".png" => "",
                ".PNG" => "",
                ".JPG" => "",
                ".jpg" => "",
                ".jpeg" => "",
                ".JPEG" => "",
                ".bmp" => "",
                ".pdf" => "",
            );
            $uploadedFileUrl = Cloudinary::uploadFile($image->getRealPath())->getSecurePath();
        }
    }
    return $uploadedFileUrl;
}


function uploadVideo($video)
{
    if (isset($video)) {
        if ($video->isValid()) {
            $trans = array(
                ".mp4" => "",
                ".wmv" => "",
                ".mkv" => "",
                ".mov" => "",
                ".avi" => "",
                ".webm" => "",
            );
            $uploadedFileUrl = Cloudinary::uploadVideo($video->getRealPath())->getSecurePath();
          
          return $uploadedFileUrl;
        }
    }
}

// Count the number of likes in a specific comment
function commentLikesCount($comment_id){
    $likes = Like::where([
     'liked'=> 1,
     'likeable_id'=> $comment_id,
     'likeable_type'=>'App\Models\Comment',
    ])->get();

    return count($likes);
}

function replyLikeCounts($reply_id){
       $likes = Like::where([
     'liked'=> 1,
     'likeable_id' => $reply_id,
     'likeable_type' => 'App\Models\Reply',
    ])->get();

    return count($likes);
}

// Get the user that like a specific comment
function commentLikedBy($comment_id){
    if(auth()->user()){
    $like = Like::where([
     'liked'=> 1,
     'user_id'=> authUserId(),
     'likeable_id'=> $comment_id,
     'likeable_type'=>'App\Models\Comment',
    ])->first();
    if($like && $like->isLikedBy->id == authUserId()){
      return 'text-primary';
    }else{
      return 'text-default';
    }
}
}

function replyLikedBy($reply_id){
    if(auth()->user()){
    $like = Like::where([
     'liked'=> 1,
     'user_id'=> authUserId(),
     'likeable_id' => $reply_id,
     'likeable_type' => 'App\Models\Reply',
    ])->first();
    if($like && $like->isLikedBy->id == authUserId()){
      return 'text-primary';
    }else{
      return 'text-default';
    }
}
}

function checkLoggedInUser(){
  
    return auth()->user();
  
}

function getFirstLetterOfUserNames($full_name) {
    $result = '';
    foreach (explode(' ', $full_name) as $word)
        $result .= strtoupper($word[0]);
    return $result;
}

function  videoIsLikedBy($videoId)
 {
  return  Like::where([
                        ['liked',1],
                        ['user_id', authUserId()],
                        ['likeable_id', $videoId],
                        ['likeable_type', 'App\Models\Video']
                     ])->with(['isLikedBy'])->first();
}