<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'First Name',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your first name',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'First name must be at least {{ limit }} characters long',
                        'maxMessage' => 'First name cannot be longer than {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s\-\']+$/',
                        'message' => 'First name can only contain letters, spaces, hyphens, and apostrophes',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Enter your first name',
                    'class' => 'form-control',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Last Name',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your last name',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 50,
                        'minMessage' => 'Last name must be at least {{ limit }} characters long',
                        'maxMessage' => 'Last name cannot be longer than {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z\s\-\']+$/',
                        'message' => 'Last name can only contain letters, spaces, hyphens, and apostrophes',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Enter your last name',
                    'class' => 'form-control',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter your email',
                    ]),
                    new Email([
                        'message' => 'Please enter a valid email address',
                    ]),
                    new Length([
                        'max' => 180,
                        'maxMessage' => 'Email cannot be longer than {{ limit }} characters',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Enter your email',
                    'class' => 'form-control',
                    'autocomplete' => 'email',
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => [
                    'attr' => [
                        'class' => 'form-control',
                        'autocomplete' => 'new-password',
                    ],
                ],
                'required' => true,
                'first_options' => [
                    'label' => 'Password',
                    'attr' => [
                        'placeholder' => 'Create a password',
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirm Password',
                    'attr' => [
                        'placeholder' => 'Confirm your password',
                    ],
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 8,
                        'max' => 4096,
                        'minMessage' => 'Password must be at least {{ limit }} characters long',
                        'maxMessage' => 'Password cannot be longer than {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
                        'message' => 'Password must contain at least one lowercase letter, one uppercase letter, and one number',
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

