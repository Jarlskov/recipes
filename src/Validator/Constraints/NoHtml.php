<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class NoHtml extends Constraint
{
    public string $message = 'This field cannot contain HTML tags.';

    public function getTargets(): string
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
