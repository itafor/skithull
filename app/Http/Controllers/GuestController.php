<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
    	$data['videos'] = Video::orderBy("created_at", "desc")->with(['user'])->paginate(12);

        return view('welcome', $data);
    }
}
