<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Validator\Constraints\NoScript;
use App\Validator\Constraints\NoScriptValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class NoScriptValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): NoScriptValidator
    {
        return new NoScriptValidator();
    }

    public function testNoScriptValidatorWithValidInput(): void
    {
        $this->validator->validate('This is plain text', new NoScript());
        $this->assertNoViolation();
    }

    public function testNoScriptValidatorWithScriptTag(): void
    {
        $this->validator->validate('<script>alert("xss")</script>', new NoScript());
        $this->buildViolation('This field cannot contain script tags or JavaScript.')
            ->setParameter('{{ value }}', '"<script>alert("xss")</script>"')
            ->assertRaised();
    }

    public function testNoScriptValidatorWithJavaScriptUrl(): void
    {
        $this->validator->validate('javascript:alert("xss")', new NoScript());
        $this->buildViolation('This field cannot contain script tags or JavaScript.')
            ->setParameter('{{ value }}', '"javascript:alert("xss")"')
            ->assertRaised();
    }

    public function testNoScriptValidatorWithEventHandler(): void
    {
        $this->validator->validate('onclick="alert(\'xss\')"', new NoScript());
        $this->buildViolation('This field cannot contain script tags or JavaScript.')
            ->setParameter('{{ value }}', '"onclick="alert(\'xss\')""')
            ->assertRaised();
    }

    public function testNoScriptValidatorWithEvalFunction(): void
    {
        $this->validator->validate('eval("malicious code")', new NoScript());
        $this->buildViolation('This field cannot contain script tags or JavaScript.')
            ->setParameter('{{ value }}', '"eval("malicious code")"')
            ->assertRaised();
    }

    public function testNoScriptValidatorWithExpressionFunction(): void
    {
        $this->validator->validate('expression("malicious code")', new NoScript());
        $this->buildViolation('This field cannot contain script tags or JavaScript.')
            ->setParameter('{{ value }}', '"expression("malicious code")"')
            ->assertRaised();
    }

    public function testNoScriptValidatorWithEmptyInput(): void
    {
        $this->validator->validate('', new NoScript());
        $this->assertNoViolation();
    }

    public function testNoScriptValidatorWithNullInput(): void
    {
        $this->validator->validate(null, new NoScript());
        $this->assertNoViolation();
    }
}
