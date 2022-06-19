<?php

namespace App\Services;

use App\Entity\Commande;
use App\Repository\ProduitRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class CommandeService
{
    private $em;
    private $produitRepo;

    public function __construct(EntityManagerInterface $em, ProduitRepository $produitRepo)
    {
        $this->em = $em;
        $this->produitRepo = $produitRepo;
    }

    public function creationCmd($panier)
    {
        $cmd = new Commande(); // On remplit la table Commande
        $cmd->setNumero($cmd->genererNum());
        $cmd->setDateCommande(new DateTime());
        $cmd->setAdresseLivraison($panier->get);
    }
}
