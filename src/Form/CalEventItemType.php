<?php

namespace Celtic34fr\CalendarCore\Form;

use Celtic34fr\CalendarCore\FormEntity\CalEventItem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;

class CalEventItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, [
                'required' => false,
            ])
            ->add('cle', TextType::class, [
                'label' => "Clé d'accès",
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                    new Length([
                        'min' => 4, 'minMessage' => "La clé doit faire au moins 4 caractères",
                        'max' => 16, "maxMessage" => "La clé doit faire au plus 16 caractères"
                    ])
                ],
            ])
            ->add('fonction', TextType::class, [
                'label' => "Descriptif",
                'constraints' => [
                    new NotBlank(),
                    new NotNull(),
                    new Length([
                        'min' => 4, 'minMessage' => "La description doit faire au moins 4 caractères",
                        'max' => 255, "maxMessage" => "La description doit faire au plus 255 caractères"
                    ])
                ],
            ])
            ->add('border', ColorType::class, [
                'label' => "Couleur de fond",
                'html5' => true,
                'empty_data' => "#FFFFFF",
            ])
            ->add('text', ColorType::class, [
                'label' => "Couleur de bordure",
                'html5' => true,
                'empty_data' => "#FFFFFF",
            ])
            ->add('background', ColorType::class, [
                'label' => "Couleur d'écriture",
                'html5' => true,
                'empty_data' => "#000000",
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CalEventItem::class,
        ]);
    }
}
