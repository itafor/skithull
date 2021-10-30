<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [GuestController::class, 'index']);


require __DIR__.'/auth.php';

Auth::routes();
Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/upload/video', [VideoController::class, 'createNewVideo'])->name('video.create');
Route::post('/create/video', [VideoController::class, 'storeNewVideo'])->name('video.store');
Route::get('/my-videos', [VideoController::class, 'myVideos'])->name('my.videos');
Route::get('/video/watch/{uuid}', [VideoController::class, 'watchVideo'])->name('video.watch');

// video comments
Route::post('/comment/add', [CommentController::class, 'writeComment'])->name('video.comment.add');
Route::get('/video/comments/{video_id}', [CommentController::class, 'displayVideoComments']);
Route::get('/video/comments/{video_id}/{no_of_page}', [CommentController::class, 'loadCommentsWithPagination']);
Route::post('/video-comment/update', [CommentController::class, 'editComment']);
Route::get('/video-comment/destroy/{commentId}', [CommentController::class, 'destroyComment']);
Route::get('/video/refresh-comments/{video_id}', [CommentController::class, 'refreshVideoCommentsAfterDelete']);


Route::group([
    'prefix' => 'video-reply'
], function () {
    Route::post('/write', [ReplyController::class, 'writeReply']);
    Route::post('update', [ReplyController::class, 'editReply']);
    Route::get('destroy/reply/{reply_id}/{commentId}', [ReplyController::class, 'destroyReply']);
    Route::get('{comment_id}/{reply_id}', [ReplyController::class, 'viewReply']);
    Route::get('comment/reply/{id}', [ReplyController::class, 'getRepliesByComment']);
    Route::get('display-more-replies/{commentId}/{numberOfReplies}', [ReplyController::class, 'displayMoreReplies']);
});

Route::group([
    'prefix' => 'like'
], function () {
    Route::get('video/{video_id}', [LikeController::class, 'likeVideo']);
    Route::get('comment/{commentId}/{video_id}', [LikeController::class, 'likeUnlikeComment']);
    Route::get('reply/{reply_id}/{video_id}/{commentId}', [LikeController::class, 'likeUnlikeReply']);
});
