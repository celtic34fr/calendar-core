<?php

namespace Celtic34fr\CalendarCore\Form;

use Celtic34fr\CalendarCore\Form\CalEventItemType;
use Celtic34fr\CalendarCore\FormEntity\CalEventItems;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalEventItemsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('items', CollectionType::class, [
                'label' => 'Liste des catégories',
                'entry_type' => CalEventItemType::class,
                'allow_add' => true,
                'allow_delete' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // 'data_class' => CalEventItems::class,
        ]);
    }
}
