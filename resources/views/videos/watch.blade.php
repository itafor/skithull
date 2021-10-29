@extends('layouts.app')

@section('content')
<div class="container">
  <div class="col-md-12">
  <div class="col-md-8 pull-left">
    <div class="card">
        <iframe height="500"  src="{{$video->video_url}}" title="YouTube video player" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      <div class="card-body">
        <h5 class="card-title">{{$video->video_title}}</h5>
        <span> &nbsp;<b>{{Str::limit($video->author, 20)}}</b></span>
        <small>&nbsp;&nbsp; <i class="fa fa-clock-o" aria-hidden="true"></i> &nbsp;{{$video->created_at->diffForHumans()}}</small>
        <div class="">
    <span class="mr-2"> <i class="fa fa-thumbs-up "></i> 250 Likes</span>
    <span class="mr-2">Edit</span>
    <span class="mr-2 text-danger">Delete</span>
    <span class="mr-2" title="Report"><i class="fa fa-flag"></i></span>
  </div>

    @include('videos.comments.commentForm')
          <hr>
          <p id="lessVideoDescription">
          {{Str::limit($video->description, 210) }} 
                  @if(strlen($video->description) > 210)
              <b onclick="seeMoreVideoDescriotion({{$video->id}})" style="cursor:pointer;">See more</b>
              @endif
          </p>
          <p style="display: none;" id="moreVideoDescription">{!! $video->description !!} <b onclick="seeLessVideoDescription()" style="cursor: pointer;">&nbsp;See Less</b></p>
<hr>
 <!-- Comments and replies -->
   <br />
  <div><strong id="numberOfVideoComments">{{count($video->comments)}} </strong> Comments </div>
  <hr>
   <div id="display_comment"></div>
        <p class="text-center loading">Loading...</p>
      </div>
    </div>
  </div>

  <div class="col-md-4 pull-right">
    <h2>More videos</h2>
    @if(isset($allvideos) && count($allvideos) >=1)
    @foreach($allvideos as $video)
        @include('videos.partials.video_card')
      @endforeach
  {{$allvideos->links('pagination::bootstrap-4')}}
  </div>
@else
<span>No videos found</span>
@endif
 
</div>
</div>

<script type="text/javascript">
  function seeMoreVideoDescriotion() {
    $("#lessVideoDescription").hide();
    $("#moreVideoDescription").show();
}

function seeLessVideoDescription() {
    $("#lessVideoDescription").show();
    $("#moreVideoDescription").hide();
}

// add new comment to video
$('#videoCommentform').on('submit', function(e) {
       e.preventDefault(); 
       if(!loggedInUser){
        alert('Please login to write comments.');
        return;
       }
       var commentBody = $('#comment_body').val();
       var videoId = $('#video_id').val();
       $.ajax({
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           type: "POST",
           url: baseUrl+'/comment/add',
           data: {body:commentBody, video_id:videoId},
           success: function(data) {
            $("#numberOfVideoComments").html("");
            $("#numberOfVideoComments").html(data.noumberOfComments ? data.noumberOfComments : "0");
               $("#video_comment_holder").html("");
                   $('#display_comment').html(data.comments);

                   $("#comment_body").val("")
           }
       });
   });

// reply to video comments
function submitVideoCommentReplyForm(commentId){

      if(!loggedInUser){
        alert('Please login to write reply.');
        return;
       }
       var replyBody = $('#reply_body'+commentId).val();
       var comment_Id = $('#comment_id'+commentId).val();
       $.ajax({
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           type: "POST",
           url: baseUrl+'/video-reply/write',
           data: {body:replyBody, comment_id:comment_Id},
           success: function(data) {
               $("#video_comment_holder").html("");
                   $('#display_comment').html(data.comments);
                $('#reply_body'+commentId).val("");
               $("#repliesContainer"+commentId).show();

           }
       });
   }

 function  saveUpdatedComment(commentId){
   if(!loggedInUser){
        alert('Please login to update comment.');
        return;
       }
       var editable_comment_body = $('#editable_comment_body'+commentId).val();
       var commentId = $('#editable_comment_id'+commentId).val();

       $.ajax({
           headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
           type: "POST",
           url: baseUrl+'/video-comment/update',
           data: {body:editable_comment_body, comment_id:commentId},
           success: function(data) {

            if(data.errors){
               alert(data.errors);
               return;
            }
          
               $("#video_comment_holder").html("");
                   $('#display_comment').html(data.comments);

           }
       });

   }
//Delete video comment and refresh page
   function deleteVideoComment(commentId){
     $.ajax({
           type: "GET",
           url: baseUrl+'/video-comment/destroy/'+commentId,
           dataType: 'json',
           success: function(data) {
        if(video){
          refreshVideoCommentsAfterDeleting(video.id);
      }    
       
           }
       });
   }

   // Like or unlike a comment
function likeOrDislikeComment(commentId, video_id){
          
          if(!loggedInUser){
        alert('Please login to like comment.');
        return;
       }
       $.ajax({
           type: "GET",
           url: baseUrl+'/like/comment/'+commentId+'/'+video_id,
           dataType: 'json',
           success: function(data) {
            console.log(data.comments);
               $("#video_comment_holder").html("");
                   $('#display_comment').html(data.comments);
           }
       });
   }

      // like or unlike a reply
function likeOrUnlikeReply(reply_id, video_id, commentId){
            console.log('likes data',commentId);

          if(!loggedInUser){
        alert('Please login to like reply.');
        return;
       }
       $.ajax({
           type: "GET",
           url: baseUrl+'/like/reply/'+reply_id+'/'+video_id+'/'+commentId,
           dataType: 'html',
           success: function(data) {
            // console.log('likes data',data);

               // $("#repliesContainer"+commentId).show();
               $("#repliesContainer"+commentId).html("");
               $("#repliesContainer"+commentId).html(data);

           }
       });
   }



</script>
@endsection


