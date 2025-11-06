<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class PostController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|image|max:5120', // keep validation
        ]);

        $post = Post::create($validated);

        // Safely handle featured image only when a valid UploadedFile is present
        $file = $request->file('featured_image');
        if ($file instanceof UploadedFile && $file->isValid()) {
            try {
                // optional: clear previous featured media
                $post->clearMediaCollection('featured');

                // Use the real filesystem path to avoid passing null to Spatie's drivers
                $post->addMedia($file->getRealPath())
                    ->usingFileName($file->getClientOriginalName())
                    ->toMediaCollection('featured');
            } catch (\Throwable $e) {
                Log::error('Featured image processing failed on store: ' . $e->getMessage(), [
                    'exception' => $e,
                    'post_id' => $post->id,
                ]);
                // Do not rethrow; the request should continue successfully
            }
        }

        return redirect()->route('posts.index')->with('success', 'Post created.');
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'featured_image' => 'nullable|image|max:5120',
        ]);

        $post->update($validated);

        $file = $request->file('featured_image');
        if ($file instanceof UploadedFile && $file->isValid()) {
            try {
                // remove old featured image(s) if desired
                $post->clearMediaCollection('featured');

                $post->addMedia($file->getRealPath())
                    ->usingFileName($file->getClientOriginalName())
                    ->toMediaCollection('featured');
            } catch (\Throwable $e) {
                Log::error('Featured image processing failed on update: ' . $e->getMessage(), [
                    'exception' => $e,
                    'post_id' => $post->id,
                ]);
            }
        }

        return back()->with('success', 'Post updated.');
    }

    public function index(Request $request)
    {
        // Ensure media is eager loaded
        $posts = Post::with('media')
            ->where('type', 'news')   // adjust to your filter if needed
            ->latest()
            ->paginate(12);

        return view('news.index', compact('posts'));
    }
}