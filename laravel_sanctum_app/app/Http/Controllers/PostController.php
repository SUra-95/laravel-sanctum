<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index(){
        // return Post::paginate(10);
        
        $data = Post::paginate(10);
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);
    }
    public function createPost(Request $request){
        // return Post::get();

        $data = $request->validate([
            'title' => ['required','string','min:3'],
            'content' => ['required','string','max:5000'],
            'user_id' => ['required','exists:users,id'],
        ]);
        return Post::create($data);
        // return response()->json([
        //     'message' => 'success'
        // ]);
    }
}
