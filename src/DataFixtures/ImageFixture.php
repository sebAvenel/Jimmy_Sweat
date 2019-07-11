<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class ImageFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < 100; $i++) {
            $image = new Image();
            $image
                /*->setName($faker->imageUrl(250, 150))*/
                ->setName('bf4880c7471af0cec83c3d73df6a4d2a.jpeg')
                ->setTrick($this->getReference('trick-' . rand(0, 29)));
            $manager->persist($image);
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
