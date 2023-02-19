<?php

namespace App\Controller;

use App\Entity\Unicorn;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UnicornController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em
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

        return new JsonResponse($data);
    }

}