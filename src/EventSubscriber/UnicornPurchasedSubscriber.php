<?php

namespace App\EventSubscriber;

use App\Event\UnicornPurchasedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UnicornPurchasedSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            UnicornPurchasedEvent::NAME => 'onUnicornPurchased',
        ];
    }

    public function onUnicornPurchased(UnicornPurchasedEvent $event)
    {
        ray($event);

        $userEmail = $event->getUser()->getEmailAddress();
    }
}