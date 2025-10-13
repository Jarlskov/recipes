<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\Rating;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Recipe Name',
                'attr' => [
                    'placeholder' => 'Enter recipe name',
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Recipe name is required']),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Recipe name must be at least {{ limit }} characters',
                        'maxMessage' => 'Recipe name cannot be longer than {{ limit }} characters'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Describe this recipe...',
                    'class' => 'form-control',
                    'rows' => 3
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 1000,
                        'maxMessage' => 'Description cannot be longer than {{ limit }} characters'
                    ])
                ]
            ])
            ->add('steps', CollectionType::class, [
                'label' => 'Cooking Steps',
                'entry_type' => TextareaType::class,
                'entry_options' => [
                    'label' => false,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Enter a cooking step...',
                        'class' => 'form-control',
                        'rows' => 2
                    ],
                    'constraints' => [
                        new Assert\Length([
                            'max' => 500,
                            'maxMessage' => 'Step cannot be longer than {{ limit }} characters'
                        ])
                    ]
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'required' => false,
                'attr' => [
                    'class' => 'steps-collection'
                ]
            ])
            ->add('ingredients', CollectionType::class, [
                'label' => 'Ingredients',
                'entry_type' => IngredientType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'required' => false,
                'attr' => [
                    'class' => 'ingredients-collection'
                ]
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Rating',
                'required' => false,
                'choices' => $this->getRatingChoices(),
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Additional notes, tips, or variations...',
                    'class' => 'form-control',
                    'rows' => 3
                ],
                'constraints' => [
                    new Assert\Length([
                        'max' => 1000,
                        'maxMessage' => 'Notes cannot be longer than {{ limit }} characters'
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }

    /**
     * Generate rating choices dynamically from the Rating enum
     */
    private function getRatingChoices(): array
    {
        $choices = ['No Rating' => null];
        
        foreach (Rating::cases() as $rating) {
            $choices[sprintf('%s (%d)', $rating->getLabel(), $rating->value)] = $rating;
        }
        
        return $choices;
    }
}
