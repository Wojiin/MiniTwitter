<?php

namespace App\Event;

use App\Entity\Post;

final class PostCreatedEvent
{
    public function __construct(
        private Post $post,
    ) {
    }

    public function getPost(): Post
    {
        return $this->post;
    }
}
