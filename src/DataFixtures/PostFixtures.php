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
        $postCount = 40;
        $users = $manager->getRepository(User::class)->findAll();
        $unicorns = $manager->getRepository(Unicorn::class)->findAll();
        $paddedUnicornArray = array_pad($unicorns, $postCount, null);
        shuffle($paddedUnicornArray);

        for ($i = 0; $i < $postCount; $i++) {
            $post = new Post();
            $post->setBody($this->faker->paragraphs(3, true));
            $post->setUser($users[array_rand($users)]);
            $post->setUnicorn($paddedUnicornArray[$i]);
            $post->setDeleted($this->faker->boolean(5));
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