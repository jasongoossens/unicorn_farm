<?php

namespace App\Controller;

use App\Entity\Unicorn;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UnicornController extends AbstractController
{
    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly LoggerInterface        $apiLogger,
    )
    {
    }

    #[Route('/unicorns/', name: 'all_unicorns', methods: ['GET'])]
    public function getAllUnicorns(Request $request): JsonResponse
    {
        $repository = $this->em->getRepository(Unicorn::class);
        $unicorns = $repository->findAll();

        if (!$unicorns) {
            return new JsonResponse('No unicorns found');
        }

        $data = [];
        foreach ($unicorns as $unicorn) {
            $data[] = [
                'id' => $unicorn->getId(),
                'name' => $unicorn->getName(),
                'owner' => $unicorn->getUser()?->getId() ?? 'no owner'
            ];
        }

        $this->apiLogger->info('Queried all unicorns', ['route' => $request->attributes->get('_route')]);

        return new JsonResponse($data);
    }

}