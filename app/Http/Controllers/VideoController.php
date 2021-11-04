<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoResource;
use App\Http\Resources\VideoResourceCollection;
use App\Models\Like;
use App\Models\Video;
use App\Models\VideoView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Validator;

class VideoController extends Controller
{

       /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['watchVideo']);
    }


    public function createNewVideo(Request $request)
    {
    	return view('videos.create');
    }

    public function myVideos(Request $request)
    {
        $data['videos'] = Video::where('user_id', authUserId())->orderBy("created_at", "desc")->with(['user'])->paginate(12);

        // $videos = response(new VideoResourceCollection($data['myvideos']));

    	return view('videos.myvideos', $data);
    }

     public function watchVideo($uuid, $title_slug)
    {
        $data['active_video'] = Video::where('uuid', $uuid)->first();
        $data['video'] = Video::where('uuid', $uuid)->first();
        views($data['video'])->record();//Record views



            $this->createVideoViews($data['video']->id);

    	$data['videoLikesCount'] = count(Like::where([
            ['likeable_id', $data['video']->id],
            ['likeable_type', 'App\Models\Video'],
            ['liked', 1],
        ])->get());

        $data['likeBy'] = videoIsLikedBy($data['video']->id);
        $data['title_slug'] = $title_slug;
        $data['video_comments'] = $data['video']->comments;
    	$data['allvideos'] = Video::orderBy("created_at", "desc")->paginate(5);
        $data['views_count'] =  views($data['video'])->unique()->count(); //views($data['video'])->count();// $this->getVideoViews($data['video']->id);
        
      $data['video_description'] =  strip_tags($data['video']->description);


    	return view('videos.watch', $data);
    }

     public function editVideo($uuid)
    {
        $data['video'] =  Video::where('uuid', $uuid)->first();

        return view('videos.edit', $data);
    }

  
     public function storeNewVideo(Request $request)
    {
       $data = $request->all();
    	// dd($data);

    	 $validator = Validator::make($data, [
            'video_title' => ['required'],
            // 'video_url' => ['required', 'max:255'],
            'channel' => ['required', 'max:255'],
            'author' => ['required'],
        ]);

    	 if ($validator->fails()) {
            return back()->withErrors($validator)
                        ->withInput()->with('error', 'Please fill in a required fields');
        }

        $video = Video::uploadNewVideo($data);
        if($video){

        	return back()->withSuccess('video uploaded successfully!');
        }

    	 return back()->withInput()->withFail('An attempt to upload video failed');  
    }

  public function updateVideo(Request $request)
    {
       $data = $request->all();

         $validator = Validator::make($data, [
            'video_title' => ['required'],
            'channel' => ['required', 'max:255'],
            'author' => ['required'],
        ]);

         if ($validator->fails()) {
            return back()->withErrors($validator)
                        ->withInput()->with('error', 'Please fill in a required fields');
        }
        if(isset($data['video_url']) && $data['video_url'] != ''){
        $video = Video::where('uuid', $data['video_uuid'])->first();
        if($video){
     $video->video_title = $data['video_title'];
     $video->channel = $data['channel'];
     $video->author = $data['author'];
     $video->title_slug = Str::slug($data['video_title'], '-');
     $video->description = isset($data['description']) ? $data['description'] : null;
     $video->video_url = isset($data['video_url']) && $data['channel'] == 'youTube'  ? Video::embedYoutubeVideo($data) : uploadVideo($data['video_url']);

     $video->save();

            return back()->withSuccess('video updated successfully!');
        }

         return back()->withInput()->withFail('An attempt to update video failed');  
     }else{
         return back()->withInput()->withFail('The video url field is required!');  
     }
    }

   /**
     * delete a reply
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function destroyVideo($video_uuid)
    {
        $video =  Video::where([
        ['user_id', authUserId()],
        ['uuid',$video_uuid],
        ])->first();
        
        if ($video) {
            $video->delete();
            return  redirect()->route('my.videos')->withSuccess('Video deleted successfully!');
           
        }
        
        return response()->json(['error' => 'Something went wrong, please try again']);
    }
  
    public function createVideoViews($video_id)
    {
        $ipAddr = \Request::ip();

            $view = VideoView::where([
                ['video_id', $video_id],
                ['ip', $ipAddr],
            ])->first();

            if(!$view){
        $video_view = new VideoView();
        $video_view->video_id = $video_id;
        $video_view->ip = $ipAddr;
        $video_view->save();
            }
    }

     public function getVideoViews($video_id)
    {

           $views = VideoView::where([
                ['video_id', $video_id],
            ])->get();

      return count($views);
          
    }

    public function recordNewView($video, $video_id){
        $video_views = DB::table('views')->where([
             ['viewable_id' => $video_id],
            ['viewable_type' => 'App\Models\Video'],
            ['visitor', $visitor]
        ])->get();
    }
}
