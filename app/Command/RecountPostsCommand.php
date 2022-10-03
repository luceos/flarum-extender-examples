<?php

namespace App\Command;

use Flarum\Discussion\Discussion;
use Illuminate\Console\Command;

/**
 * @see https://discuss.flarum.org/d/31716-migrating-forum-question-about-post-counts
 */
class RecountPostsCommand extends Command
{
    protected $signature = 'recount-posts';
    protected $description = 'Recounts posts for all discussions.';

    public function handle()
    {
        Discussion::query()
            // Removes all permission checks so that we can get all discussions
            ->withoutGlobalScopes()
            // Loop over the result set, chunked hunk and all.
            ->each(function (Discussion $discussion) {
                $this->info("$discussion->id-$discussion->slug");

                $discussion->refreshCommentCount();
            });
    }
}
