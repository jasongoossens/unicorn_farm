<?php

namespace App\DataFixtures;

use App\Entity\Unicorn;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UnicornFixtures extends BaseFixture implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $users = $manager->getRepository(User::class)->findAll();

        for ($i = 0; $i < 10; $i++) {
            $unicorn = new Unicorn();
            $unicorn->setName($this->faker->firstName());
            $manager->persist($unicorn);

            if ($this->faker->boolean(30)) {
                $unicorn->setUser($users[array_rand($users)]);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}