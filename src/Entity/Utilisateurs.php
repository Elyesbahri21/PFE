<?php

namespace App\Entity;

use App\Repository\UtilisateursRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UtilisateursRepository::class)
 */
class Utilisateurs
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
    private $Utilisateurs;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateurs(): ?string
    {
        return $this->Utilisateurs;
    }

    public function setUtilisateurs(string $Utilisateurs): self
    {
        $this->Utilisateurs = $Utilisateurs;

        return $this;
    }
}
