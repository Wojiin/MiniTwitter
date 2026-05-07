<?php

namespace App\Event;

use App\Entity\Reply;

final class ReplyCreatedEvent
{
    public function __construct(
        private Reply $reply,
    ) {
    }

    public function getReply(): Reply
    {
        return $this->reply;
    }
}
