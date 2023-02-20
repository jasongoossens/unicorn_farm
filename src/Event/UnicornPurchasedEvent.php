<?php

namespace App\Event;

use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UnicornPurchasedEvent extends Event
{
    public const NAME = 'unicorn.purchased';

    public function __construct(
        protected User $user
    )
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }
}