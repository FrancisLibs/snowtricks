<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PictureRepository")
 * @Vich\Uploadable
 */
class Picture
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var File|null
     * @Assert\Image(
     *     mimeTypes="image/jpeg"
     * )
     * @Vich\UploadableField(mapping="trick_image", fileNameProperty="filename")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filename;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Trick", inversedBy="pictures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $trick;

    /**
     * @ORM\OneToOne(targetEntity=Trick::class, mappedBy="mainPicture")
     */
    private $mainPictureTrick;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $mainPicture;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(?string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    /**
     * @return null|File
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param null|File $imageFile
     * @return self
     */
    public function setImageFile(?File $imageFile): self
    {
        $this->imageFile = $imageFile;
        return $this;
    }

    public function getMainPictureTrick(): ?Trick
    {
        return $this->mainPictureTrick;
    }

    public function setMainPictureTrick(?Trick $mainPictureTrick): self
    {
        $this->mainPictureTrick = $mainPictureTrick;

        // set (or unset) the owning side of the relation if necessary
        $newMainPicture = null === $mainPictureTrick ? null : $this;
        if ($mainPictureTrick->getMainPicture() !== $newMainPicture) {
            $mainPictureTrick->setMainPicture($newMainPicture);
        }

        return $this;
    }

    public function getMainPicture(): ?bool
    {
        return $this->mainPicture;
    }

    public function setMainPicture(?bool $mainPicture): self
    {
        $this->mainPicture = $mainPicture;

        return $this;
    }
}
