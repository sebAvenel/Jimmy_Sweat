<?php

namespace App\DataFixtures;

use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class VideoFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++){
            $video = new Video();
            $video
                ->setCode($faker->url)
                ->setTrick($this->getReference('trick-' . rand(0, 9)));
            $manager->persist($video);
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
