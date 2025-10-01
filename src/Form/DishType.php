<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Dish;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Validator\Constraints\NoHtml;
use App\Validator\Constraints\NoScript;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class DishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a dish name',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Dish name must be at least {{ limit }} characters long',
                        'maxMessage' => 'Dish name cannot be longer than {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9\s\-\'\.\,\&]+$/',
                        'message' => 'Dish name can only contain letters, numbers, spaces, hyphens, apostrophes, periods, commas, and ampersands',
                    ]),
                    new NoHtml(),
                    new NoScript(),
                ],
                'attr' => [
                    'placeholder' => 'Enter dish name',
                    'class' => 'form-control',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'constraints' => [
                    new Length([
                        'max' => 1000,
                        'maxMessage' => 'Description cannot be longer than {{ limit }} characters',
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9\s\-\'\.\,\&\:\;\!\?\(\)]+$/',
                        'message' => 'Description contains invalid characters',
                    ]),
                    new NoHtml(),
                    new NoScript(),
                ],
                'attr' => [
                    'placeholder' => 'Describe this dish (optional)',
                    'class' => 'form-control',
                    'rows' => 4,
                ],
            ])
            ->add('dailyMealFriendly', CheckboxType::class, [
                'label' => 'Daily Meal Friendly',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
            ->add('prepFriendly', CheckboxType::class, [
                'label' => 'Prep Friendly',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
            ->add('freezerFriendly', CheckboxType::class, [
                'label' => 'Freezer Friendly',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dish::class,
        ]);
    }
}

