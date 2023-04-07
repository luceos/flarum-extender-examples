<?php

namespace App\User\Hash;

use Illuminate\Hashing\BcryptHasher;

class WrappedBcryptSha extends BcryptHasher
{
    public function make($value, array $options = [])
    {
        return parent::make($this->sha($value), $options);
    }

    public function check($value, $hashedValue, array $options = [])
    {
        return parent::check($this->sha($value), $hashedValue, $options);
    }

    protected function sha($value)
    {
        return hash('sha256', $value);
    }
}
