<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    //
    public function index(){
        return response(
            [
                'posts' =>Post::orderBy('created_at', 'desc')->with('user:id,name,image')->withCount('comments', 'likes')->get()
            ], 200
            );
    }

    public function show($id){
        return response(
            [
                'post'=> Post::where('id', $id)->withCount('comments', 'likes')->get()
            ],
            200
        );
    }

    public function  store(Request $request){
        $attrs = $request->validate(
            [
                'body'=> 'required'
            ]
        );
       
      $request->all();
    $request['user_id'] = auth()->user()->id;

        $post = Post::create(
         $request->all()
        );


        return response([
            'message' =>'Post created',
            'post'=> $post
        ], 200);
    }


    
    public function  update(Request $request, $id){
       
        $post = Post::findOrFail($id);

        if(!$post){
            return response([
                'message'=> 'Post not  found'
            ], 403);
        }

        if($post->user_id != auth()->user()->id){
            return response(
                [
                    'message'=> 'Permission denied'
                ], 403
            );
        }
        
        $request->validate(
            [
                'body'=> 'required|string'
            ]
        );
        $post->update($request->all());
      

        return response([
            'message' =>'Post updated',
            'post'=> $post
        ], 200);
    }


    public function destroy($id){
        $post = Post::findOrFail($id);

        if(!$post){
            return response([
                'message'=> 'Post not  found'
            ], 403);
        }

        if($post->user_id != auth()->user()->id){
            return response(
                [
                    'message'=> 'Permission denied'
                ]
            );
        }
        $post->comments()->delete();
        $post->likes()->delete();

        $post->delete();

        return response(
            [
                'message'=>'Post deleted'
            ],
            200
        );
    }
}
