<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\Constraints\NoHtml;
use App\Validator\Constraints\NoHtmlValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class NoHtmlValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): NoHtmlValidator
    {
        return new NoHtmlValidator();
    }

    public function testNoHtmlValidatorWithValidInput(): void
    {
        $this->validator->validate('This is plain text', new NoHtml());
        $this->assertNoViolation();
    }

    public function testNoHtmlValidatorWithHtmlInput(): void
    {
        $this->validator->validate('<p>This has HTML</p>', new NoHtml());
        $this->buildViolation('This field cannot contain HTML tags.')
            ->setParameter('{{ value }}', '"<p>This has HTML</p>"')
            ->assertRaised();
    }

    public function testNoHtmlValidatorWithComplexHtmlInput(): void
    {
        $this->validator->validate('<div><span>Complex HTML</span></div>', new NoHtml());
        $this->buildViolation('This field cannot contain HTML tags.')
            ->setParameter('{{ value }}', '"<div><span>Complex HTML</span></div>"')
            ->assertRaised();
    }

    public function testNoHtmlValidatorWithEmptyInput(): void
    {
        $this->validator->validate('', new NoHtml());
        $this->assertNoViolation();
    }

    public function testNoHtmlValidatorWithNullInput(): void
    {
        $this->validator->validate(null, new NoHtml());
        $this->assertNoViolation();
    }
}
