@extends('layouts.app')

@section('content')
<div class="container">
    <div class="container-fluid mt--7 main-container">
        <div class="row">
            <div class="col-xl-12 order-xl-1">
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <h3 class="mb-0">{{ __('Manage video') }}</h3>
                            </div>
                            <div class="col-4 text-right">
                               <a href="" class="btn-icon btn-tooltip" title="{{ __('Back To List') }}"><i class="las la-angle-double-left"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                      @include('alerts.message')

                        <form method="post" action="{{ route('video.update') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="pl-lg-4 pr-lg-4">
                                <input type="hidden" name="video_uuid" value="{{$video->uuid}}">
                                <div class="row">
                                        <div class="col-xl-4">
                                        <div class="form-group{{ $errors->has('video_title') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="video_title">{{ __('Video Title *') }}</label>
                                            <input type="text" name="video_title" id="video_title" class="form-control form-control-alternative{{ $errors->has('video_title') ? ' is-invalid' : '' }}" placeholder="{{ __('Enter video title') }}" value="{{ old('video_title', $video->video_title) }}" required autofocus>

                                            @if ($errors->has('video_title'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('video_title') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-4">
                                        <div class="form-group{{ $errors->has('author') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="author">{{ __('Author') }}</label>
                                            <input type="text" name="author" id="author" class="form-control form-control-alternative{{ $errors->has('author') ? ' is-invalid' : '' }}" placeholder="{{ __('Enter author') }}" value="{{ old('author', $video->author) }}" required autofocus>

                                            @if ($errors->has('author'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('author') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                     <div class="col-xl-4">
                                        <div class="form-group{{ $errors->has('channel') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="channel">{{ __('Channel') }}</label>
                                             <select name="channel" id="channel" class="form-control border-input" data-toggle="select" required>
                                                <option value="">Choose a channel</option>
                                                <option value="{{$video->channel}}" selected="selected">{{$video->channel}}</option>
                                                <option value="youTube">From YouTube</option>
                                                <option value="device">From Device</option>
                                                    
                                            </select>
                                            @if ($errors->has('channel'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('channel') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                   
                                </div>

                                 <div class="col-xl-12" id="device_video_url_holder" style="display: {{$video->channel == 'device' ? 'block' : 'none';}};">
                                        <div class="form-group{{ $errors->has('video_url') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="video_url">{{ __('Select video from device') }}</label>
                                            <input type="file" name="video_url" id="device_video_url" class="form-control form-control-alternative border-input{{ $errors->has('video_url') ? ' is-invalid' : '' }}" placeholder="{{ __('video_url') }}" value="{{ old('video_url') }}" >

                                            @if ($errors->has('video_url'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('video_url') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                        <iframe height="200"  src="{{$video->video_url}}" title="YouTube video player" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                        <a  href="{{route('video.watch',[$video->uuid, $video->title_slug])}}" class="btn btn-primary btn-sm">Watch</a>

                                    </div>

                                       <div class="col-xl-12" id="youtube_video_url_holder" style="display: {{$video->channel == 'youTube' ? 'block' : 'none';}};">
                                        <div class="form-group{{ $errors->has('video_url') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="video_url">{{ __('Video url (YouTube)') }}</label>
                                            <input type="url" name="video_url" id="youtube_video_url" class="form-control form-control-alternative border-input{{ $errors->has('video_url') ? ' is-invalid' : '' }}" placeholder="{{ __('Enter video link from youtube') }}" value="{{ old('video_url', $video->channel == 'youTube' ? $video->video_url : '') }}"  pattern="[Hh][Tt][Tt][Pp][Ss]?:\/\/(?:(?:[a-zA-Z\u00a1-\uffff0-9]+-?)*[a-zA-Z\u00a1-\uffff0-9]+)(?:\.(?:[a-zA-Z\u00a1-\uffff0-9]+-?)*[a-zA-Z\u00a1-\uffff0-9]+)*(?:\.(?:[a-zA-Z\u00a1-\uffff]{2,}))(?::\d{2,5})?(?:\/[^\s]*)?">

                                            @if ($errors->has('video_url'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('video_url') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                         <iframe height="200"  src="{{$video->video_url}}" title="YouTube video player" frameborder="0" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                                       <a  href="{{route('video.watch',[$video->uuid, $video->title_slug])}}" class="btn btn-primary btn-sm">Watch</a>

                                    </div>


                                         <div class="col-xl-12" >
                                        <div class="form-group{{ $errors->has('description') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="description">{{ __('Description') }}</label>
                                            <textarea name="description" id="summernote" class="form-control form-control-alternative border-input{{ $errors->has('description') ? ' is-invalid' : '' }}" placeholder="{{ __('Enter video link from youtube') }}" value="{{ old('description') }}" rows="5">{!!$video->description!!}</textarea>

                                            @if ($errors->has('description'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('description') }}</strong>
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success mt-4">{{ __('Upload Video') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<script type="text/javascript" src="{{url('js/createVideo.js')}}"></script>
@endsection
