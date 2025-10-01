<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class NoScript extends Constraint
{
    public string $message = 'This field cannot contain script tags or JavaScript.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
