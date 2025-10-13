<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\FoodItem;
use App\Entity\Ingredient;
use App\Service\Quantity;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class IngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('foodItemName', TextType::class, [
                'label' => 'Ingredient',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Type ingredient name...',
                    'class' => 'form-control food-item-autocomplete',
                    'autocomplete' => 'off'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter an ingredient name',
                    ]),
                    new Assert\Length([
                        'min' => 2,
                        'max' => 255,
                        'minMessage' => 'Ingredient name must be at least {{ limit }} characters',
                        'maxMessage' => 'Ingredient name cannot be longer than {{ limit }} characters'
                    ])
                ]
            ])
            ->add('amount', NumberType::class, [
                'label' => 'Amount',
                'required' => true,
                'attr' => [
                    'placeholder' => 'e.g., 2',
                    'class' => 'form-control',
                    'step' => '0.1',
                    'min' => '0'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please enter an amount',
                    ]),
                    new Assert\Positive([
                        'message' => 'Amount must be positive',
                    ])
                ]
            ])
            ->add('unit', ChoiceType::class, [
                'label' => 'Unit',
                'required' => true,
                'choices' => $this->getUnitChoices(),
                'placeholder' => 'Select unit...',
                'attr' => [
                    'class' => 'form-control'
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'Please select a unit',
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ingredient::class,
        ]);
    }

    /**
     * Get available unit choices from the Quantity service
     */
    private function getUnitChoices(): array
    {
        return Quantity::getSupportedUnitsWithNames();
    }
}
