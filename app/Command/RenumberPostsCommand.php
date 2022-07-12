<?php

namespace App\Command;

use Flarum\Discussion\Discussion;
use Flarum\Post\Post;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

/**
 * @see https://discuss.flarum.org/d/31185-posts-do-not-have-numbers-assigned-in-discussions
 */
class RenumberPostsCommand extends Command
{
    protected $signature = 'renumber-posts';
    protected $description = 'Resorts posts numbers of posts without them.';

    public function handle()
    {
        Discussion::query()
            // Removes all permission checks so that we can get all discussions
            ->withoutGlobalScopes()
            // Filter discussions that have posts without number
            ->whereHas('comments', function (Builder $query) {
                $query->whereNull('number');
            })
            // Loop over the result set, chunked hunk and all.
            ->each(function (Discussion $discussion) {
                $this->info("$discussion->id-$discussion->slug");

                // Start at 1.
                $number = 1;

                // Loop over all posts from the discussion, this can take a while.
                $discussion
                    ->posts()
                    ->withoutGlobalScopes()
                    ->orderBy('created_at')
                    ->each(function (Post $post) use (&$number) {
                        $post->number = $number;

                        $this->constraints(function () use ($post) {
                            $post->save();
                        });

                        $number++;
                    });

                $this->info(" - Max number stored $number");
            });
    }

    protected function constraints(callable $execute)
    {
        Post::query()->getConnection()->getSchemaBuilder()->enableForeignKeyConstraints();

        $execute();

        Post::query()->getConnection()->getSchemaBuilder()->disableForeignKeyConstraints();
    }
}
