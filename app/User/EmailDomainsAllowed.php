<?php


namespace App\User;


use Flarum\Extend\ExtenderInterface;
use Flarum\Extend\Validator as Extend;
use Flarum\Extension\Extension;
use Flarum\User\UserValidator;
use Illuminate\Contracts\Container\Container;
use Illuminate\Validation\Validator;

class EmailDomainsAllowed implements ExtenderInterface
{
    /**
     * @var array|string
     */
    private $domains;

    /**
     * EmailDomainsAllowed constructor.
     *
     * @param string|array $domains : edu.com
     */
    public function __construct($domains)
    {
        $this->domains = (array) $domains;
    }

    public function extend(Container $container, Extension $extension = null)
    {
        (new Extend(UserValidator::class))
            ->configure(function (UserValidator $user, Validator $validator) {
                $rules = $validator->getRules()['email'];

                $rules[] = 'ends_with:' . implode(',', $this->domains);

                $validator->addRules(['email' => $rules]);
            });
    }
}
