<?php

namespace App\Controller;

use App\Entity\Produit;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BoutiqueController extends AbstractController
{
    /**
     * @Route("/boutique", name="index_boutique")
     */
    public function index(ManagerRegistry $doctrine): Response
    {
        $produits = $doctrine->getRepository(Produit::class)->findAll();

        return $this->render('boutique/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    /**
     * @Route("/boutique/{id}", name="detail_produit")
     */
    public function detailProduit(Produit $produit): Response
    {
        return $this->render('boutique/produit.html.twig', [
            'produit' => $produit,
        ]);
    }
}
