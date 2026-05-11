<?php

namespace App\Event;

use App\Entity\Contact;

final class ContactRequestCreatedEvent
{
    public function __construct(
        private Contact $contact,
    ) {
    }

    public function getContact(): Contact
    {
        return $this->contact;
    }
}
