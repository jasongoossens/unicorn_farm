<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Entity\Unicorn;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $users = $manager->getRepository(User::class)->findAll();
        $unicorns = $manager->getRepository(Unicorn::class)->findAll();

        for ($i = 0; $i < 40; $i++) {
            $post = new Post();
            $post->setBody($this->faker->paragraphs(3, true));
            $post->setUser($users[array_rand($users)]);

            // post can have a unicorn, but it can occasionally be null
            /* @var Unicorn $unicorn */
            $unicorn = $unicorns[rand(0, count($unicorns) + 1)] ?? null;
            $post->setUnicorn($unicorn);

            // if a unicorn has an owner/user, the post will have been deleted
            $post->setDeleted(false);
            if (!$unicorn?->getUser()) {
                $post->setDeleted(true);
            }

            $manager->persist($post);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            UnicornFixtures::class,
        ];
    }
}