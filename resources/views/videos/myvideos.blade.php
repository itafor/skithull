@extends('layouts.app')

@section('content')
<div class="container">
                      @include('alerts.message')
   <div class="row">
    @if(isset($videos) && count($videos) >=1)
    @foreach($videos as $video)
  <div class="col-sm-3">
     @include('videos.partials.video_card')
  </div>
  @endforeach
  <span style="margin-left: 200px; margin-top: 20px;">
  {{$videos->links('pagination::bootstrap-4')}}
  </span>
@else
<span>No videos found</span>
@endif
</div>
</div>
@endsection


