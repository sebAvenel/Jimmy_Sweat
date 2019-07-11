<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TrickRepository")
 */
class Trick
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="tricks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="trick", cascade={"persist", "remove"})
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="trick", cascade={"persist", "remove"})
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Video", mappedBy="trick", cascade={"persist", "remove"})
     */
    private $videos;

    /**
     * @ORM\Column(type="integer")
     */
    private $groups;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstImage;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->updated_at = new \DateTime();
        $this->created_at = new \DateTime();
        $this->validated = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setTrick($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getTrick() === $this) {
                $message->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setTrick($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getTrick() === $this) {
                $image->setTrick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Video[]
     */
    public function getVideos(): Collection
    {
        return $this->videos;
    }

    public function addVideo(Video $video): self
    {
        if (!$this->videos->contains($video)) {
            $this->videos[] = $video;
            $video->setTrick($this);
        }

        return $this;
    }

    public function removeVideo(Video $video): self
    {
        if ($this->videos->contains($video)) {
            $this->videos->removeElement($video);
            // set the owning side to null (unless already changed)
            if ($video->getTrick() === $this) {
                $video->setTrick(null);
            }
        }

        return $this;
    }

    public function getGroups(): ?int
    {
        return $this->groups;
    }

    public function setGroups(int $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    public function getFirstImage(): ?string
    {
        return $this->firstImage;
    }

    public function setFirstImage(?string $firstImage): self
    {
        $this->firstImage = $firstImage;

        return $this;
    }

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
     * Controls if the images collection associated with a trick matches an image array, if an element does not match, it is erased
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
