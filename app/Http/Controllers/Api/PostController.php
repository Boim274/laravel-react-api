<?php

namespace App\Http\Controllers\Api;

// import model post
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\post;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class PostController extends Controller
{
    public function index()
    {
        $posts = post::latest()->paginate(5);
 
        return new PostResource(true, 'List Data Post', $posts);
    }

    public function store(Request $request)
    {
        // devine validation rules
        $Validator = Validator ::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'title' => 'required',
            'content' => 'required',
        ]);
 
        // check if validations fails
        if ($Validator->fails()) {
            return response()->json($Validator->errors(), 422);
        }
 
        // upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());
 
        // create post
        $post = Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content' => $request->content,
        ]);
 
        // return response
        return new PostResource(true, 'Data Post Berhasil Di Tambahkan', $post);
    }

    public function show($id)
    {
        $post = Post::find($id);
        return new PostResource(true, 'Detail Data Post', $post);
    }

    public function update(Request $request, $id)
    {
        // devine validation rules
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
        ]);
        // check validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $post = Post::find($id);
        // check post by ID
        if ($request->hasFile('image')) {
            // upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());
            // delete old image
            Storage::delete('public/posts/' . basename($post->image));
            // update post woth new image
            $post->update([
                'image' => $image->hashName(),
                'title' => $request->title,
                'content' => $request->content,
            ]);
        } else {
            $post->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);
        }
        // return response
        return new PostResource(true, 'Data Post Berhasil Di Ubah!', $post);
    }

    public function destroy($id)
    {
        // find post by ID
        $post = Post::find($id);
        // delete image
        Storage::delete('public/posts/' . basename($post->image));
        // delete post
        $post->delete();
        // return response
        return new PostResource(true, 'Data Post Berhasil Di Hapus!', null);
    }
}
