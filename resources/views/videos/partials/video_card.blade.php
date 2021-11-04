 <div class="card">
        <iframe  src="{{$video->video_url}}" title="Video player" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
      <div class="card-body">
         <a href="{{route('video.watch',[$video->uuid, $video->title_slug])}}" style="text-decoration: none; color: #000;"> 
        <h5 class="card-title" title="{{$video->video_title}}">{{Str::limit($video->video_title, 15)}}</h5>
        <span class="mt-10" title="{{$video->author}}">&nbsp;<b>{{Str::limit($video->author, 10)}}</b></span>
        <small>&nbsp;&nbsp; <i class="fa fa-clock-o" aria-hidden="true"></i> &nbsp;{{$video->created_at->diffForHumans()}}</small>
          </a>
     <br>
        <small>
        <a  href="{{route('video.watch',[$video->uuid, $video->title_slug])}}" class="btn btn-primary btn-sm">Watch</a>
        </small>
      </div>
    </div>