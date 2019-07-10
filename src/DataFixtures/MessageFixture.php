<?php

namespace App\DataFixtures;

use App\Entity\Message;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class MessageFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 1000; $i++) {
            $message = new Message();
            $message
                ->setContent($faker->words(50, true))
                ->setCreatedAt($faker->dateTime('now', null))
                ->setUpdatedAt($faker->dateTime('now', null))
                ->setValidated(rand(0, 1))
                ->setTrick($this->getReference('trick-' . rand(0, 29)))
                ->setUser($this->getReference('user-' . rand(0,99)));
            $manager->persist($message);
        }
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }

    /**
     * This method must return an array of fixtures classes
     * on which the implementing class depends on
     * @return array
     */
    public function getDependencies()
    {
        return [UserFixture::class, TrickFixture::class];
    }
}
