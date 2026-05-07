<?php

namespace App\Event;

use App\Entity\Message;

final class DiscussionMessageCreatedEvent
{
    public function __construct(
        private Message $message,
    ) {
    }

    public function getMessage(): Message
    {
        return $this->message;
    }
}
