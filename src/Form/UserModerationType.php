<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserModerationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('status', ChoiceType::class, [
                'label' => 'Statut du compte',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-select',
                ],
                'choices' => [
                    'Actif' => 'actif',
                    'Suspendu' => 'suspendu',
                    'Banni' => 'banni',
                ],
            ])
            ->add('suspended_until', DateTimeType::class, [
                'label' => 'Fin de suspension',
                'required' => false,
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'help' => 'Renseignez une date si le compte est suspendu. Laissez vide pour lever la suspension ou pour une suspension sans date.',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'help_attr' => [
                    'class' => 'form-help',
                ],
                'attr' => [
                    'class' => 'form-input',
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
