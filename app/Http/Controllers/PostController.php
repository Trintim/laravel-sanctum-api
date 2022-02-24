<?php

namespace App\Http\Controllers;

use App\Http\Library\ApiHelpers;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use ApiHelpers;

    public function post(Request $request): JsonResponse
    {
        if($this->isAdmin($request->user())){
            $post = DB::table('posts')->get();
            return $this->onSuccess($post, 'Post Retrieved');
        }
        return $this->onError(401, 'Unauthorized Access');
    }

    public function singlePost(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if($this->isAdmin($user) || $this->isSubAdmin($user) || $this->isAssociates($user)){
            $post = DB::table('posts')->where('id', $id)->first();
            if(!empty($post)){
                return $this->onSuccess($post, 'Post Retrieved');
            }
            return $this->onError(404, 'Post not Found');
        }
        return $this->onError(401, 'Unauthorized Access');
    }

    public function createPost(Request $request): JsonResponse
    {
        $user = $request->user();
        if($this->isAdmin($user) || $this->isSubAdmin($user) || $this->isAssociates($user)){
            $validator = Validator::make($request->all(), $this->postValidationRules());
            if($validator->passes()){
                $post = new Post();
                $post->title = $request->input('title');
                $post->slug = Str::slug($request->input('title'));
                $post->content = $request->input('content');
                $post->save();

                return $this->onSuccess($post, 'Post Created');
            }
            return $this->onError(400, $validator->errors());
        }
        return $this->onError(401, 'Unauthorized Access');
    }

    public function updatePost(Request $request, $id)
    {
        $user = $request->user();
        if($this->isAdmin($user) || $this->isSubAdmin($user) || $this->isAssociates($user)){
            $validator = Validator::make($request->all(), $this->postValidationRules());
            if($validator->passes()){
                $post = Post::find($id);
                $post->title = $request->input('title');
                $post->slug = Str::slug($request->input('title'));
                $post->content = $request->input('content');
                $post->save();

                return $this->onSuccess($post, 'Post Updated');
            }
            return $this->onError(400, $validator->errors());
        }
        return $this->onError(401, ' Unauthorized Access');
    }

    public function deletePost(Request $request, $id): JsonResponse
    {
        $user = $request->user();
        if($this->isAdmin($user) || $this->isSubAdmin($user) || $this->isAssociates($user)){
            $post = Post::find($id);
            $post->delete();
            if(!empty($post)){
                return $this->onSuccess($post, 'Post Deleted');
            }
            return $this->onError(404, 'Post Not Found');
        }
        return $this->onError(401, 'Unauthorized Access');
    }

}
