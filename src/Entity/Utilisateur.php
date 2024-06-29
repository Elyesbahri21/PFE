<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 */
class Utilisateur
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
    private $Id_utilisateur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $Name_utilisateur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $gg;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUtilisateur(): ?string
    {
        return $this->Id_utilisateur;
    }

    public function setIdUtilisateur(string $Id_utilisateur): self
    {
        $this->Id_utilisateur = $Id_utilisateur;

        return $this;
    }

    public function getNameUtilisateur(): ?string
    {
        return $this->Name_utilisateur;
    }

    public function setNameUtilisateur(string $Name_utilisateur): self
    {
        $this->Name_utilisateur = $Name_utilisateur;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getGg(): ?string
    {
        return $this->gg;
    }

    public function setGg(string $gg): self
    {
        $this->gg = $gg;

        return $this;
    }
}
