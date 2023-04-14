<?php

namespace App\User;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class MyProfileRedirection implements RequestHandlerInterface
{

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        /** @var \Flarum\Http\UrlGenerator $url */
        $url = resolve(\Flarum\Http\UrlGenerator::class);

        $actor = \Flarum\Http\RequestUtil::getActor($request);

        if ($actor?->exists && ! $actor->isGuest()) {
            return new \Laminas\Diactoros\Response\RedirectResponse($url->to('forum')->route('user', ['username' => $actor->username]));
        }

        return new \Laminas\Diactoros\Response\EmptyResponse(404);
    }
}
