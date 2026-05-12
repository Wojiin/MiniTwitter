<?php

namespace App\Form;

use App\Entity\Discussion;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;

class DiscussionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $currentUser */
        $currentUser = $options['current_user'];

        $builder
            ->add('title', null, [
                'required' => false,
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-input',
                ],
            ])
            ->add('first_message', TextareaType::class, [
                'mapped' => false,
                'label' => 'Premier message',
                'required' => false,
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-textarea',
                ],
            ])
            ->add('first_message_picture', FileType::class, [
                'label' => 'Image du premier message',
                'mapped' => false,
                'required' => false,
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-file',
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
            ->add('discuss', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'user_name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'Choisir les participants',
                'label_attr' => [
                    'class' => 'form-label',
                ],
                'attr' => [
                    'class' => 'form-choice-group',
                ],
                'query_builder' => static function (UserRepository $repository) use ($currentUser) {
                    $queryBuilder = $repository->createQueryBuilder('u')
                        ->orderBy('u.user_name', 'ASC');

                    if ($currentUser instanceof User && null !== $currentUser->getId()) {
                        $queryBuilder
                            ->andWhere('u.id != :currentUserId')
                            ->setParameter('currentUserId', $currentUser->getId());
                    }

                    return $queryBuilder;
                },
                'constraints' => [
                    new Count(
                        min: 1,
                        minMessage: 'Choisissez au moins un utilisateur pour demarrer une discussion.'
                    ),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Discussion::class,
            'current_user' => null,
        ]);

        $resolver->setAllowedTypes('current_user', ['null', User::class]);
    }
}
