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
        $userRole = ['user', 'admin'];
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 100; $i++){
            $user = new User();
            $user
                /*->setAvatar($faker->imageUrl(150, 150))*/
                ->setAvatar(null)
                ->setName($faker->userName)
                ->setPassword($faker->password)
                ->setEmail($faker->email)
                ->setRole($userRole[rand(0, 1)])
                ->setToken($faker->md5)
                ->setActivated(rand(0, 1));
            $manager->persist($user);

            $this->addReference('user-' . $i, $user);
        }

        /* Create user */
        $user = new User();
        $user
            ->setAvatar(null)
            ->setName('user')
            ->setPassword('$2y$12$eDTMC9wjDgO72fJZOQhseu26Faq2T30oNo3vz/t47tbslnpIB/5om')
            ->setEmail('testuser@user.com')
            ->setRole('user')
            ->setToken($faker->md5)
            ->setActivated(1);
        $manager->persist($user);
        $this->addReference('user-100', $user);

        /* Create admin */
        $user = new User();
        $user
            ->setAvatar(null)
            ->setName('admin')
            ->setPassword('$2y$12$PaZNf0QT/e9h.V0hy8LVAOfRBhZBNVrdcc7zXjqMwf5.87mT52BTu')
            ->setEmail('testadmin@admin.com')
            ->setRole('admin')
            ->setToken($faker->md5)
            ->setActivated(1);
        $manager->persist($user);
        $this->addReference('user-101', $user);

        $manager->flush();
    }
}
