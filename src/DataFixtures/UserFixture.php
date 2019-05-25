<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++){
            $user = new User();
            $user
                ->setAvatar($faker->imageUrl(150, 150))
                ->setName($faker->userName)
                ->setPassword($faker->password)
                ->setEmail($faker->email)
                ->setRole($faker->word)
                ->setToken($faker->md5)
                ->setActivated(rand(0, 1));
            $manager->persist($user);

            $this->addReference('user-' . $i, $user);
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
