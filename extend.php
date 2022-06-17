<?php

namespace App;

// I do this so that it is obvious we are using Flarum native extenders
use Flarum\Extend as Flarum;
use Flarum\Post\Event\Saving;

return [
    // Users that register require an email that ends
    // with flarum.org
    new User\EmailDomainsAllowed('@flarum.org'),
    // You can also use an array:
    // new User\EmailDomainsAllowed(['@flarum.org', '@flarum.com'])

    (new Flarum\Event)
        // Modify imgur url's to be prefixed, php 7.4+ only
        ->listen(Saving::class, fn(Saving $event) => str_replace('https://i.imgur.com', 'https://discuss.grapheneos.org/image-proxy/i.imgur.com', $event->post->content)),
];
