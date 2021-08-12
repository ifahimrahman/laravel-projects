<?php

namespace App\Http\Controllers;
use App\Http\Requests\StorePost;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only(['create','store','edit','update','destroy']);
    }

    public function index()
    {
        // DB::enableQueryLog();
        // $post =BlogPost::with('comments')->get();
        // foreach ($post as $post){
        //     foreach ($post->comments as $comment){
        //         echo $comment->content;
        //     }
        // }
        // dd(DB::getQueryLog());

        return view('posts.index',
        [
            'posts'=>BlogPost::latest()->withCount('comments')->get(), 
            'mostCommented'=>BlogPost::mostCommented()->take(5)->get(),
            'mostActive' => User::withMostBlogPosts()->take(5)->get(),
            'mostActiveLastMonth' => User::withMostBlogPostsLastMonth()->take(5)->get()
        ]
    );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $this->authorize('posts.create');
        return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePost $request)
    {

    $validatedData = $request->validated();
    $validatedData['user_id']=$request->user()->id;
    // dd($validatedData);
    
    //    $title = $request->input('title');
    //    $content = $request->input('content');
    //    dd($title,$content);
    // $blogPost= new BlogPost();
    // $validatedData['title'];
    // $validatedData['content'];
    // $blogPost ->title = $request->input('title');
    // $blogPost ->content = $request->input('content');
    // $blogPost ->save();
    
    $blogPost = BlogPost::create($validatedData);

    // $hasFile = $request->hasFile('thumbnail');
    //     dump($hasFile);

    //     if ($hasFile) {
    //         $file = $request->file('thumbnail');
    //         dump($file);
    //         dump($file->getClientMimeType());
    //         dump($file->getClientOriginalExtension());

    //         dump($file->store('thumbails'));
    //     }
    //     die; 
    if ($request->hasFile('thumbnail')) {
        $path = $request->file('thumbnail')->store('thumbnails');
        $blogPost->image()->save(
            Image::create(['path' => $path])
        );
    }
   

    $request->session()->flash('status', 'Blog post was created!');

    return redirect()->route('posts.index', ['post' => $blogPost->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {   
        // $request->session()->reflash();
        // return view('posts.show', ['post'=>BlogPost::with(['comments'=>function($query){
        //     return $query->latest();
        // }])->findOrFail($id)]);
        $request->session()->reflash();
        return view('posts.show', ['post'=>BlogPost::with('comments')->with('user')->with('comments.user')->findOrFail($id)]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = BlogPost::findOrFail($id);

        $this->authorize('update',$post);
        // if (Gate::denies('update-post', $post)) {
        //     abort(403, "You can't edit this blog post!");
        // }
        return view('posts.edit', ['post' => $post]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StorePost $request, $id)
    {
        $post = BlogPost::findOrFail($id);
        
        
        $this->authorize('update',$post);
        // if (Gate::denies('update-post', $post)) {
        //     abort(403, "You can't edit this blog post!");
        // }
        $validatedData = $request->validated();
        $post->fill($validatedData);
        
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('thumbnails');

            if ($post->image) {
                Storage::delete($post->image->path);
                $post->image->path = $path;
                $post->image->save();
            } else {
                $post->image()->save(
                    Image::create(['path' => $path])
                );
            }
        }
            

        $post->save();
        $request->session()->flash('status','Blog Post was updated!');
        return redirect()->route('posts.show',['post'=>$post->id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        $post = BlogPost::findOrFail($id);
      
        $this->authorize('delete',$post);
        // if (Gate::denies('delete-post', $post)) {
        //     abort(403, "You can't delete this blog post!");
        // }
        $post->delete();
        $request->session()->flash('status','Blog Post was deleted!');
        return redirect()->route('posts.index');
    }
}
