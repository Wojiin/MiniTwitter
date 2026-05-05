<?php

namespace App\Form;

use App\Entity\Discussion;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

class DiscussionParticipantsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $currentUser */
        $currentUser = $options['current_user'];
        /** @var Discussion|null $discussion */
        $discussion = $options['discussion'];

        $excludedIds = [];

        if ($currentUser instanceof User && null !== $currentUser->getId()) {
            $excludedIds[] = $currentUser->getId();
        }

        if ($discussion instanceof Discussion) {
            foreach ($discussion->getDiscuss() as $participant) {
                if (null !== $participant->getId()) {
                    $excludedIds[] = $participant->getId();
                }
            }
        }

        $builder
            ->add('participants', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'user_name',
                'multiple' => true,
                'expanded' => true,
                'mapped' => false,
                'label' => 'Ajouter des participants',
                'query_builder' => static function (UserRepository $repository) use ($excludedIds) {
                    $queryBuilder = $repository->createQueryBuilder('u')
                        ->orderBy('u.user_name', 'ASC');

                    if ([] !== $excludedIds) {
                        $queryBuilder
                            ->andWhere('u.id NOT IN (:excludedIds)')
                            ->setParameter('excludedIds', array_values(array_unique($excludedIds)));
                    }

                    return $queryBuilder;
                },
                'constraints' => [
                    new Count(
                        min: 1,
                        minMessage: 'Choisissez au moins un utilisateur a ajouter.'
                    ),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'current_user' => null,
            'discussion' => null,
        ]);

        $resolver->setAllowedTypes('current_user', ['null', User::class]);
        $resolver->setAllowedTypes('discussion', ['null', Discussion::class]);
    }
}
