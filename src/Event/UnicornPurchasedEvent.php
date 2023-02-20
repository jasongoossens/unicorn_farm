<?php

namespace App\Event;

use App\Entity\Unicorn;
use App\Entity\User;
use Symfony\Contracts\EventDispatcher\Event;

class UnicornPurchasedEvent extends Event
{
    public const NAME = 'unicorn.purchased';

    public function __construct(
        protected User $user,
        protected Unicorn $unicorn,
    )
    {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUnicorn(): Unicorn
    {
        return $this->unicorn;
    }
}