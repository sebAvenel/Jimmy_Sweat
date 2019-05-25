<?php

namespace App\DataFixtures;

use App\Entity\Trick;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class TrickFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $trick = new Trick();
            $trick
                ->setName($faker->word)
                ->setDescription($faker->word(100, true))
                ->setCreatedAt($faker->dateTime('now', null))
                ->setUpdatedAt($faker->dateTime('now', null))
                ->setValidated(rand(0, 1))
                ->setUser($this->getReference('user-' . rand(0, 99)));
            $manager->persist($trick);

            $this->addReference('trick-' . $i, $trick);
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
        return [UserFixture::class];
    }
}
