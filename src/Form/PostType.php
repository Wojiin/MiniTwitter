<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre',
                'attr' => [
                    'class' => 'form-input',
                    'placeholder' => 'Donnez un titre a votre publication',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])
            ->add('content', null, [
                'label' => 'Contenu',
                'attr' => [
                    'class' => 'form-textarea',
                    'placeholder' => 'Qu\'avez-vous envie de partager aujourd\'hui ?',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Photo du post',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-file',
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'constraints' => [
                    new File(
                        maxSize: '5000k',
                        mimeTypes: [
                            'image/*',
                        ],
                        mimeTypesMessage: 'Image trop lourde',
                    ),
                ],
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class, 
                'choice_label' => 'category',
                'multiple' => true,
                'required' => false,
                'label' => 'Tag',
                'placeholder' => 'Ajouter un tag',
                'attr' => [
                    'class' => 'form-select-multiple',
                    'size' => 5,
                ],
                'label_attr' => [
                    'class' => 'form-label',
                ],
            ])

            // ->add('created_at', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('updated_at', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('count_repost')
            // ->add('count_like')
            // ->add('status')
            // ->add('count_flag')
            // ->add('creator', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('users', EntityType::class, [
            //     'class' => User::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
