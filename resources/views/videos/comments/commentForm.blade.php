 <form class="mt-2" method="POST"  id="videoCommentform">
     @csrf
    <input type="hidden" name="video_id" id="video_id" value="{{$video->id}}">
    <div class="input-group mb-3">
 <textarea name="body" id="comment_body" class="form-control" value="" placeholder="Write a comment" required></textarea> 
  <div class="input-group-append">
    <button type="submit" class="input-group-text" id="basic-addon2">Comment</button>
  </div>
</div>
  </form>