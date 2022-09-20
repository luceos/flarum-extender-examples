<?php

use Carbon\Carbon;
use Flarum\Discussion\Discussion;
use Flarum\Extend;
use Flarum\Post\CommentPost;
use Flarum\Post\Event\Saving;
use Flarum\User\User;
use Illuminate\Support\Arr;
use Psr\Http\Message\ServerRequestInterface;

return [
    (new Extend\Console)
        ->command(App\Command\RenumberPostsCommand::class),
    (new Extend\Console)
        ->command(App\Command\RenumberPostsCommand::class),

    // Users that register require an email that ends
    // with flarum.org
    new App\User\EmailDomainsAllowed('@flarum.org'),
    // You can also use an array:
    // new User\EmailDomainsAllowed(['@flarum.org', '@flarum.com'])

    (new Extend\Event)
        // Modify imgur url's to be prefixed, php 7.4+ only
        ->listen(Saving::class, fn(Saving $event) => str_replace('https://i.imgur.com', 'https://discuss.grapheneos.org/image-proxy/i.imgur.com', $event->post->content)),

    // throttle all members
    (new Extend\ThrottleApi())
        ->set('throttle-members', function (ServerRequestInterface $request) {
            // Get the route name being requested.
            $routeName = $request->getAttribute('routeName');

            // Ignore requests to routes we don't want to apply throttling to.
            if ($routeName !== 'posts.create') return;

            // The current user.
            /** @var User $actor */
            $actor = $request->getAttribute('actor');

            // Ignore users with more groups than one.
            if ($actor->groups()->count() > 1 ) return;

            // Restrict the number of comment posts to one in this time frame.
            // Eg once per minute: $after = Carbon::now()->subMinute();
            $after = Carbon::now()->subMinutes(2);

            // The function needs to return true if we want to throttle and false if not
            // We will load the posts by this user and if there's at least one
            // we will throttle the user.
            return CommentPost::query()
                // remove any global scopes that might restrict the query, eg approvals
                ->withoutGlobalScopes()
                // which are by this user
                ->where('user_id', $actor->id)
                // created in the time frame since $after
                ->where('created_at', '>=', $after)
                // if there are more than 0, we'll return true to throttle, or null to ignore this throttler
                ->count() > 0 ? true : null;
        }),

    (new Extend\ThrottleApi())
        ->set('limit-tag-interaction', function (ServerRequestInterface $request) {
            // The tag Id we want to apply throttling on.
            $specificTagId = 5;

            // Restrict the number of discussions to one in this time frame.
            // Eg once per minute: $after = Carbon::now()->subMinute();
            $after = Carbon::now()->subMinute();

            // Get the route name being requested.
            $routeName = $request->getAttribute('routeName');

            // Ignore requests to routes we don't want to apply throttling to.
            if ($routeName !== 'discussions.create') return;

            // Get the posted raw data.
            $data = Arr::get($request->getParsedBody(), 'data', []);

            // Get the discussion creation payload relating to the tags.
            $tags = Arr::get($data, 'relationships.tags.data', []);

            // Reduce creation payload to only tag Ids
            $tags = array_map(fn ($tag) => $tag['id'], $tags);

            // Ignore discussions created without the tag in need of throttling.
            if (! in_array($specificTagId, $tags)) return;

            // The current user.
            /** @var User $actor */
            $actor = $request->getAttribute('actor');

            // The function needs to return true if we want to throttle and false if not
            // We will load the discussions by this user and if there's at least one
            // we will throttle the user.
            return Discussion::query()
                // remove any global scopes that might restrict the query, eg approvals
                ->withoutGlobalScopes()
                // which are by this user
                ->where('user_id', $actor->id)
                // created in the time frame since $after
                ->where('created_at', '>=', $after)
                // if there are more than 0, we'll return true to throttle, or null to ignore this throttler
                ->count() > 0 ? true : null;
        }),

    // ALlow HTML iframes inside posts
    (new Extend\Formatter)
        ->configure(function (\s9e\TextFormatter\Configurator $configurator) {
            $configurator->HTMLElements->allowUnsafeElement('iframe');
        }),
];
