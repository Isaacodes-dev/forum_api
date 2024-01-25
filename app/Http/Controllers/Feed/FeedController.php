<?php

namespace App\Http\Controllers\Feed;

use App\Models\Feed;
use App\Models\Like;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;

class FeedController extends Controller
{
    public function index()
    {
        $feeds = Feed::with('user')->latest()->get();
        return response([
            'feeds' => $feeds
        ],200);
    }
    public function store(PostRequest $postRequest)
    {
        $isValidated = $postRequest->validated();

        if ($isValidated) {
            auth()->user()->feeds()->create([
                'content' => $postRequest->content,
            ]);

            return response([
                'message' => 'Content Created',
            ], 201);
        } elseif (!$isValidated) {
            $postRequest->validated();
        } else {
            return response([
                'message' => 'Content Not Created',
            ], 422);
        }

    }

    public function likePost($feed_id)
    {
        $feed = Feed::whereId($feed_id)->first();
        if (!$feed) {
            return response([
                'message' => '404 not found',
            ], 404);
        }

        //unliked post
        $unliked_post = Like::where('user_id', auth()->id())->where('feed_id', $feed_id)->delete();
        if ($unliked_post) {
            return response([
                'message' => 'unliked post',
            ], 200);
        }

        //liked post
        $liked_post = Like::create([
            'user_id' => auth()->id(),
            'feed_id' => $feed_id
        ]);
        if ($liked_post) {
            return response([
                'message' => 'liked post',
            ], 201);
        }

    }

    public function comment(Request $request,$feed_id)
    {
        $isvalidated = $request->validate([
            'body' => 'required'
        ]);

        if($isvalidated)
        {
            $comment = Comment::create([
                'user_id' => auth()->id(),
                'feed_id' => $feed_id,
                'body' => $request->body
            ]);

            return response([
                'message' => 'success'
            ]);
        }
        else{
            return response([
                'error' => 'comment is required'
            ]);
        }

    }

    public function getComments($feed_id)
    {
        $comments = Comment::with('feed')->with('user')->whereFeedId($feed_id)->latest()->get();
        return response([
            'comments' => $comments
        ],200);
    }

}
