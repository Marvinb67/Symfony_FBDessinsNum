<?php

namespace App\Entity;

use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CommentaireRepository;

/**
 * @ORM\Entity(repositoryClass=CommentaireRepository::class)
 */
class Commentaire
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     */
    private $contenue;

    /**
     * @ORM\Column(type="datetime")
     */
    private $poster_le;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="commentaires")
     * @JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $utilisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Actualite::class, inversedBy="commentaires")
     * @ORM\JoinColumn(nullable=false)
     */
    private $actualite;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    public function __construct()
    {
        $this->poster_le = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenue(): ?string
    {
        return $this->contenue;
    }

    public function setContenue(string $contenue): self
    {
        $this->contenue = $contenue;

        return $this;
    }

    public function getPosterLe(): ?\DateTimeInterface
    {
        return $this->poster_le;
    }

    public function setPosterLe(\DateTimeInterface $poster_le): self
    {
        $this->poster_le = $poster_le;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getActualite(): ?Actualite
    {
        return $this->actualite;
    }

    public function setActualite(?Actualite $actualite): self
    {
        $this->actualite = $actualite;

        return $this;
    }

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }
}
