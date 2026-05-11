<?php

namespace App\Event;

use App\Entity\Discussion;
use App\Entity\User;

final class UserAddedToDiscussionEvent
{
    public function __construct(
        private Discussion $discussion,
        private User $addedUser,
        private ?User $actor = null,
    ) {
    }

    public function getDiscussion(): Discussion
    {
        return $this->discussion;
    }

    public function getAddedUser(): User
    {
        return $this->addedUser;
    }

    public function getActor(): ?User
    {
        return $this->actor;
    }
}
