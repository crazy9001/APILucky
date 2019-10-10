<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['namespace' => 'ApiController'], function () {

    Route::resource('comment', 'ApiCommentController');

    Route::post('comment/approve', [
        'as'    =>  'api.approve.comment',
        'uses'  =>  'ApiCommentController@approveComment'
    ]);

    Route::get('top/comment', [
        'as'    =>  'api.top.comment',
        'uses'  =>  'ApiCommentController@getTopComment'
    ]);

    Route::get('/comments/{pageId}/{postId}', [
        'as'    =>  'api.get.list.comment.facebook',
        'uses'  =>  'ApiCommentController@getListComment'
    ]);

});
