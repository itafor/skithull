<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

      <style type="text/css">
     /*cirled first letters of users names in comment and replies*/
   [data-letters]:before {
  content:attr(data-letters);
  display:inline-block;
  font-size:1em;
  width:2.5em;
  height:2.5em;
  line-height:2.5em;
  text-align:center;
  border-radius:50%;
  background:plum;
  vertical-align:middle;
  margin-right:1em;
  color:white;
  }
      </style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>


  <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

 <script>
    <?php 
        use App\Models\Video;
    ?>
        var baseUrl = '{{url("/")}}';
    var loggedInUser =  <?php echo json_encode(checkLoggedInUser()); ?>;


var video = <?php echo json_encode(isset($active_video) ? $active_video : null); ?>;
var comments_count = <?php 

echo json_encode(isset($active_video) ? count($active_video->comments) : null); 

?>;
 // console.log('global_comments', global_comments)
    </script>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Skithub') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            @if(Auth::user()->user_type =='admin')
                        <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/dashboard') }}">{{ __('Dashboard') }}</a>
                                </li>

                                 <li class="nav-item">
                                    <a class="nav-link" href="#">{{ __('Create Workshop') }}</a>
                                </li>

                                @else

                                  <li class="nav-item">
                                    <a class="nav-link" href="{{route('video.create')}}">{{ __('Upload video') }}</a>
                                </li>

                                  <li class="nav-item">
                                    <a class="nav-link" href="{{route('my.videos')}}">{{ __('My videos') }}</a>
                                </li>

                                 <li class="nav-item">
                                    <a class="nav-link" href="#">{{ __('All videos') }}</a>
                                </li>

                                @endif
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.1/dist/js/bootstrap.min.js" integrity="sha384-skAcpIdS7UcVUC05LJ9Dxay8AXcDYfBJqt1CJ85S/CFujBsIzCIv+l9liuYLaMQ/" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>


<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@fengyuanchen/datepicker@0.6.5/dist/datepicker.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/@fengyuanchen/datepicker@0.6.5/dist/datepicker.min.css" rel="stylesheet"> 


 <script>
   
        $('#summernote').summernote({});

$(document).ready(function(){
   


if(video){
  loadComments(video.id);

  var paginate = 5;
    loadMoreData(paginate, video.id);
    $(window).scroll(function() {
        if($(window).scrollTop() + $(window).height() >= $(document).height()) {
            paginate++;
            loadMoreData(paginate, video.id);
          }
    });
}
});

// Fetch all active video comments
 function loadComments(video_id) {
    $.ajax({
        url: baseUrl + "/video/comments/" + video_id,
        method:"GET",
        success: function (data) {
          if (data.comments) {
            $('#display_comment').html(data.comments);
          }
        },
    });
}

// refresh video comments after deleting a comment
 function refreshVideoCommentsAfterDeleting(video_id) {
    $.ajax({
        url: baseUrl + "/video/refresh-comments/" + video_id,
        method:"GET",
        success: function (data) {
          if (data.comments) {
             $("#numberOfVideoComments").html("");
            $("#numberOfVideoComments").html(data.noumberOfComments ? data.noumberOfComments : "0");
            $('#display_comment').html(data.comments);
             // console.log('all comments', data.comments)
          }
        },
    });
}

 function replyVideoComment(id) {
    $("#replyToVideoComment" + id + "form").toggle();
}

 function toggleReplies(id) {
    $("#repliesContainer" + id).toggle();
    $("#showMoreRepliesHolder" + id).toggle();
}


function toggleEditable_comment_formdiv(id){
    $("#editable_comment_formdiv" + id+'form').toggle();
}

function toggleEditable_reply_formdiv(id){
    $("#editable_reply_formdiv" + id+'form').toggle();
}

// load more comments  when user scrolled to end of the page
    function loadMoreData(paginate, video_id) {
      
        $.ajax({
            url: baseUrl + "/video/comments/" + video_id + "/" + paginate,
            type: 'get',
            datatype: 'html',
            beforeSend: function() {
                $('.loading').show();
            }
        })
        .done(function(data) {
       

            if(data.length == 0) {
                $('.loading').html('No more comments.');
                return;
              } else {
                $('.loading').hide();
       
        $('#display_comment').html("");
        $('#display_comment').html(data.comments);

              }
        })
           .fail(function(jqXHR, ajaxOptions, thrownError) {
              alert('Something went wrong.');
           });
    }

  var numberOfReplies = 1;

function showMoreReplies(commentId){
           numberOfReplies++
var noFoReplies = $("#totalNumberOfReplies"+commentId).text();

        // console.log("testing replies data", numberOfReplies)
 // event.preventDefault()
   $.ajax({
      url: baseUrl+"/video-reply/display-more-replies/" + commentId + "/" + numberOfReplies,
      method: "GET",
      datatype: "html",
      success: function(data){
        // console.log("testing replies data", data)

// console.log("totalNumberOfReplies", noFoReplies)
        if(data){
         $("#repliesContainer"+commentId).html("");

         $("#repliesContainer"+commentId).html(data);
         
var number_Visible_replies = $("#numberOfVisibleReplies"+commentId).text();
      var viewMoreRepliesText = parseInt(number_Visible_replies) === parseInt(noFoReplies) ? '' : 'See '+  (parseInt(noFoReplies) - parseInt(number_Visible_replies))+' more replies';
   $("#showMoreRepliesHolder"+commentId).html("")
   $("#showMoreRepliesHolder"+commentId).html(viewMoreRepliesText);

      }
    }
   })
       
  
}

  </script>
</body>
</html>
