<?php

namespace App;

// I do this so that it is obvious we are using Flarum native extenders
use Flarum\Extend as Flarum;

return [
    // Users that register require an email that ends
    // with flarum.org
    new User\EmailDomainsAllowed('@flarum.org')
    // You can also use an array:
    // new User\EmailDomainsAllowed(['@flarum.org', '@flarum.com']);
];
