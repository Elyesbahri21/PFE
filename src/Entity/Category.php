<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Adminenti;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdminenti(): ?string
    {
        return $this->Adminenti;
    }

    public function setAdminenti(string $Adminenti): self
    {
        $this->Adminenti = $Adminenti;

        return $this;
    }
}
