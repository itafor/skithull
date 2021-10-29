<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoResource;
use App\Http\Resources\VideoResourceCollection;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

     public function watchVideo($uuid)
    {
        $data['active_video'] = Video::where('uuid', $uuid)->first();
    	$data['video'] = Video::where('uuid', $uuid)->first();
        $data['video_comments'] = $data['video']->comments;
    	$data['allvideos'] = Video::orderBy("created_at", "desc")->paginate(5);
    	return view('videos.watch', $data);
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

  
    
}
