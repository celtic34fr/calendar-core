<?php

namespace Celtic34fr\CalendarCore\Form;

use Celtic34fr\CalendarCore\Entity\CalEvent;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', HiddenType::class, [
                'required' => false,
            ])
            ->add('start_at', DateTimeType::class, [
                'required' => true,
                'label' => 'Date/Heure de début',
            ])
            ->add('end_at', DateTimeType::class, [
                'required' => true,
                'label' => 'Date/Heure de fin',
            ])
            ->add('subject', TextType::class, [
                'required' => true,
                'label' => 'Résumé',
            ])
            ->add('details', TextType::class, [
                'required' => false,
                'label' => 'Description',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CalEvent::class,
        ]);
    }

}