<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', TextType::class, [
            ])
            ->add('user_name')
            ->add('status')
            ->add('count_flag')
            ->add('delete_flag')
            ->add('last_login_at', null, [
                'widget' => 'single_text',
            ])
            ->add('profil_picture', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
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
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('updated_at', null, [
                'widget' => 'single_text',
            ])
            ->add('likes', EntityType::class, [
                'class' => Post::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('repost', EntityType::class, [
                'class' => Post::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;

        $builder->get('roles')->addModelTransformer(new CallbackTransformer(
            fn (?array $roles): string => implode(', ', $roles ?? []),
            function (?string $rolesAsString): array {
                if (null === $rolesAsString || '' === trim($rolesAsString)) {
                    return [];
                }

                $roles = array_map('trim', explode(',', $rolesAsString));
                $roles = array_filter($roles, static fn (string $role): bool => '' !== $role);

                return array_values(array_unique($roles));
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
