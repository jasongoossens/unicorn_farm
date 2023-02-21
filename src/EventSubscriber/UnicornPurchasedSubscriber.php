<?php

namespace App\EventSubscriber;

use App\Entity\Post;
use App\Event\UnicornPurchasedEvent;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class UnicornPurchasedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected MailService                     $mailer
    )
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            UnicornPurchasedEvent::NAME => 'handlePurchase',
        ];
    }

    public function handlePurchase(UnicornPurchasedEvent $event)
    {
        $unicorn = $event->getUnicorn();
        $postRepository = $this->em->getRepository(Post::class);
        $postCount = $postRepository->deletePostsForUnicorn($unicorn);

        $this->mailer->sendCongratulationsEmail(
            $event->getUser()->getEmailAddress(),
            $unicorn->getName(),
            $postCount,
        );
    }
}