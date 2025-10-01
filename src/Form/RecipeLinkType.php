<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Rating;
use App\Entity\RecipeLink;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Validator\Constraints\NoHtml;
use App\Validator\Constraints\NoScript;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class RecipeLinkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Recipe Name',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a recipe name',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Recipe name must be at least {{ limit }} characters long',
                        'maxMessage' => 'Recipe name cannot be longer than {{ limit }} characters',
                    ]),
                    new NoHtml(),
                    new NoScript(),
                ],
                'attr' => [
                    'placeholder' => 'Enter recipe name',
                    'class' => 'form-control',
                ],
            ])
            ->add('url', UrlType::class, [
                'label' => 'Recipe URL',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a recipe URL',
                    ]),
                    new Url([
                        'message' => 'Please enter a valid URL',
                    ]),
                    new Length([
                        'max' => 500,
                        'maxMessage' => 'URL cannot be longer than {{ limit }} characters',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'https://example.com/recipe',
                    'class' => 'form-control',
                ],
            ])
            ->add('authorName', TextType::class, [
                'label' => 'Author Name',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the author name',
                    ]),
                    new Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Author name must be at least {{ limit }} characters long',
                        'maxMessage' => 'Author name cannot be longer than {{ limit }} characters',
                    ]),
                    new NoHtml(),
                    new NoScript(),
                ],
                'attr' => [
                    'placeholder' => 'Enter author name',
                    'class' => 'form-control',
                ],
            ])
            ->add('rating', ChoiceType::class, [
                'label' => 'Rating',
                'required' => false,
                'choices' => $this->getRatingChoices(),
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeLink::class,
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
