<?php

namespace App\User;

use App\User\Hash\WrappedBcryptSha;
use Flarum\Extend\ExtenderInterface;
use Flarum\Extension\Extension;
use Illuminate\Contracts\Container\Container;

class ChangeHasher implements ExtenderInterface
{

    public function extend(Container $container, Extension $extension = null)
    {
        $container->extend('hash', function () {
            return new WrappedBcryptSha;
        });
    }
}
