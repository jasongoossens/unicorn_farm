<?php

namespace App\Controller;

use App\Entity\Unicorn;
use App\Entity\User;
use App\Event\UnicornPurchasedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class TransactionController extends AbstractController
{
    public function __construct(
        protected readonly EntityManagerInterface   $em,
        protected readonly EventDispatcherInterface $dispatcher,
        protected readonly LoggerInterface          $apiLogger,
    )
    {

    }

    #[Route('/transaction/purchase/', name: 'purchase_unicorn', methods: ['POST'])]
    public function purchaseUnicorn(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['user'])) {
            return new JsonResponse('A user id is required', Response::HTTP_NOT_FOUND);
        }
        if (!isset($data['unicorn'])) {
            return new JsonResponse('A unicorn id is required', Response::HTTP_NOT_FOUND);
        }

        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->find($data['user']);
        if (!$user) {
            return new JsonResponse(sprintf('No user for id %d', $data['user']), Response::HTTP_NOT_FOUND);
        }

        $unicornRepository = $this->em->getRepository(Unicorn::class);
        $unicorn = $unicornRepository->find($data['unicorn']);
        if (!$unicorn) {
            return new JsonResponse(sprintf('No unicorn for id %d', $data['unicorn']), Response::HTTP_NOT_FOUND);
        }

        $unicorn->setUser($user);
        $this->em->persist($unicorn);
        $this->em->flush();

        $event = new UnicornPurchasedEvent($user, $unicorn);
        $this->dispatcher->dispatch($event, UnicornPurchasedEvent::NAME);

        $this->apiLogger->info('Purchased unicorn',
            [
                'route' => $request->attributes->get('_route'),
                'params' => $data,
            ]
        );

        return new JsonResponse(sprintf('Unicorn %d purchased', $data['unicorn']));
    }
}