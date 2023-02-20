<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Unicorn;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em
    )
    {
    }

    #[Route('/posts/', name: 'all_posts', methods: ['GET'])]
    public function getAllPosts(): JsonResponse
    {
        $repository = $this->em->getRepository(Post::class);
        $posts = $repository->findBy(
            ['deleted' => false],
        );

        if (!$posts) {
            return new JsonResponse('No posts found');
        }

        return new JsonResponse($this->mapPosts($posts));
    }

    #[Route('/users/{userId}/posts/', name: 'all_user_posts', methods: ['GET'])]
    public function getPostsForUser(int $userId): JsonResponse
    {
        $repository = $this->em->getRepository(Post::class);
        $posts = $repository->findBy(
            [
                'user' => $userId,
                'deleted' => false
            ],
        );

        if (!$posts) {
            return new JsonResponse('No posts for this user');
        }

        return new JsonResponse($this->mapPosts($posts));
    }

    /** Create a post with an optional favorite unicorn linked to it */
    #[Route('/posts/', name: 'create_post_for_user', methods: ['POST'])]
    public function createPostForUser(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $unicornId = $data['unicorn'] ?? null;
        $unicorn = null;
        if ($unicornId) {
            $unicornRepository = $this->em->getRepository(Unicorn::class);
            $unicorn = $unicornRepository->find($unicornId);
        }
        if ($unicornId && !$unicorn) {
            return new JsonResponse(sprintf('No unicorn found for id %d', $unicornId), Response::HTTP_NOT_FOUND);
        }

        if (!isset($data['body'])) {
            return new JsonResponse('A post body is required', Response::HTTP_NOT_FOUND);
        }
        $postBody = $data['body'];
        if (empty($postBody)) {
            return new JsonResponse('Posts are required to have a body', Response::HTTP_BAD_REQUEST);
        }

        if (!isset($data['user'])) {
            return new JsonResponse('A user id is required', Response::HTTP_NOT_FOUND);
        }
        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->find($data['user']);
        if (!$user) {
            return new JsonResponse(sprintf('No user for id %d', $data['user']), Response::HTTP_NOT_FOUND);
        }

        $post = new Post();
        $post->setUser($user);
        $post->setBody($postBody);
        if ($unicorn) {
            $post->setUnicorn($unicorn);
        }
        $this->em->persist($post);
        $this->em->flush();

        return new JsonResponse('Post created successfully');
    }

    /** Update a post with an optional favorite unicorn linked to it */
    #[Route('/users/{userId}/posts/{postId}', name: 'edit_user_post', methods: ['PATCH'])]
    public function editPostForUser(Request $request, int $userId, int $postId): JsonResponse
    {
        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(sprintf('No user found for id %d', $userId), Response::HTTP_NOT_FOUND);
        }

        $postRepository = $this->em->getRepository(Post::class);
        $post = $postRepository->findOneBy(
            [
                'id' => $postId,
                'user' => $user,
                'deleted' => false
            ]
        );

        if (!$post) {
            return new JsonResponse(
                sprintf('No post found for id %d', $postId), Response::HTTP_NOT_FOUND
            );
        }

        $data = json_decode($request->getContent(), true);

        $postBody = '';
        if (isset($data['body'])) {
            $postBody = $data['body'];

            if (empty($postBody)) {
                return new JsonResponse('Your post body cannot be empty', Response::HTTP_BAD_REQUEST);
            }
        }

        $unicorn = null;
        if (isset($data['unicorn'])) {
            $unicornId = $data['unicorn'];
            $unicornRepository = $this->em->getRepository(Unicorn::class);
            $unicorn = $unicornRepository->find($unicornId);
            if ($unicornId && !$unicorn) {
                return new JsonResponse(sprintf('No unicorn found for id %d', $unicornId), Response::HTTP_NOT_FOUND);
            }
        }

        if (!empty($postBody)) {
            $post->setBody($postBody);
        }
        if ($unicorn) {
            $post->setUnicorn($unicorn);
        }
        $this->em->persist($post);
        $this->em->flush();

        return new JsonResponse(sprintf('Updated post %d', $postId));
    }

    #[Route('/users/{userId}/posts/', name: 'delete_user_post', methods: ['DELETE'])]
    public function deletePostForUser(Request $request, int $userId): JsonResponse
    {
        $userRepository = $this->em->getRepository(User::class);
        $user = $userRepository->find($userId);

        if (!$user) {
            return new JsonResponse(sprintf('No user for id %d', $userId), Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);
        if (!isset($data['post'])) {
            return new JsonResponse('A post id is required', Response::HTTP_NOT_FOUND);
        }
        $postId = $data['post'];
        $postRepository = $this->em->getRepository(Post::class);
        $post = $postRepository->findOneBy(
            [
                'id' => $postId,
                'user' => $user,
                'deleted' => false
            ]
        );
        ray($post);

        if (!$post) {
            return new JsonResponse(
                sprintf('No post found for id %d and user %d', $postId, $userId), Response::HTTP_NOT_FOUND
            );
        }

        $post->setDeleted(true);
        $this->em->persist($post);
        $this->em->flush();

        return new JsonResponse(sprintf('Deleted post %d', $postId));
    }

    private function mapPosts(array $posts): array
    {
        $data = [];
        foreach ($posts as $post) {
            $data[] = [
                'id' => $post->getId(),
                'body' => $post->getBody(),
                'user' => $post->getUser()->getId(),
                'unicorn' => $post->getUnicorn()?->getId() ?? 'no unicorn'
            ];
        }

        return $data;
    }
}