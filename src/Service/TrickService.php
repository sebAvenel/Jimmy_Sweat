<?php

namespace App\Service;

use App\Entity\Trick;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Filesystem\Filesystem;

class TrickService
{

    /**
     * Return a videos array
     * @param Trick $trick
     * @return ArrayCollection
     */
    public  function videosArrayMaker(Trick $trick): ArrayCollection
    {
        $originalVideos = new ArrayCollection();
        foreach ($trick->getVideos() as $video){
            $originalVideos->add($video);
        }

        return $originalVideos;
    }

    /**
     * Return an images array
     * @param Trick $trick
     * @param $images_directory
     * @return ArrayCollection
     */
    public function imagesArrayMaker(Trick $trick, $images_directory): ArrayCollection
    {
        $originalImages = new ArrayCollection();
        foreach ($trick->getImages() as $image){
            $image->setFile(new File($images_directory . '/' . $image->getName()));
            $originalImages->add($image);
        }

        return $originalImages;
    }

    /**
     * Controls if the videos collection associated with a trick matches an videos array, if an element does not match, it is erased
     * @param ArrayCollection $videosArray
     * @param Trick $trick
     * @param ObjectManager $manager
     */
    public function videosEraser(ArrayCollection $videosArray, Trick $trick, ObjectManager $manager)
    {
        foreach ($videosArray as $video){
            $video->setTrick($trick);
            if (false === $trick->getVideos()->contains($video)){
                $manager->remove($video);
                $trick->removeVideo($video);
            }
        }

        return;
    }

    /**
     * Controls if the images collection associated with a trick matches an images array, if an element does not match, it is erased
     * @param ArrayCollection $imagesArray
     * @param Trick $trick
     * @param Filesystem $filesystem
     * @param $images_directory
     */
    public function imagesEraser(ArrayCollection $imagesArray, Trick $trick, Filesystem $filesystem, $images_directory, ObjectManager $manager)
    {
        foreach ($imagesArray as $image){
            $image->setTrick($trick);
            if (false === $trick->getImages()->contains($image)){
                if ($image->getName() == $trick->getFirstImage()){
                    $trick->setFirstImage(null);
                }
                $filesystem->remove($images_directory . '/' . $image->getName());
                $manager->remove($image);
                $trick->removeimage($image);
            }
        }

        return;
    }

    /**
     * @param Trick $trick
     * @param $images_directory
     */
    public function imagesManagement(Trick $trick, $images_directory)
    {
        foreach ($trick->getImages() as $image){
            if($image->getName() == null  && $image->getFile() != null){
                $file = $image->getFile();
                $fileName = md5(uniqid()).'.'.$file->guessExtension();
                $file->move(
                    $images_directory,
                    $fileName
                );
                $image->setName($fileName);
            }
        }

        return;
    }
}
