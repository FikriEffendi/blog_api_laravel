<?php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Carbon;

class PostService
{
    public function create(User $user, array $validated): Post
    {
        $post = $user->posts()->create([
            'title' => $validated['title'],
            'content' => $validated['body'],
            'published_at' => Carbon::now(),
        ]);

        $post->categories()->sync($validated['categories']);

        return $post;
    }
}
