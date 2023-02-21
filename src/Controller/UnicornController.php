<?php

namespace App\Controller;

use App\Entity\Unicorn;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UnicornController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoggerInterface        $logger,
    )
    {
    }

    #[Route('/unicorns/', name: 'all_unicorns', methods: ['GET'])]
    public function getAllUnicorns(): JsonResponse
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

        $this->logger->error('An error occurred');

        return new JsonResponse($data);
    }

}